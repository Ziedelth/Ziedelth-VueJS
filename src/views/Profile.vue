<template>
  <div v-else class="text-center">
    <div v-if="error !== null" class="container">
      <p class="alert-danger text-danger rounded fw-bold p-2">{{ error }}</p>
    </div>

    <div v-if="requestedUser !== null" class="container rounded p-2">
      <div v-if="is()" class="text-center">
        <label class="cursor-pointer" for="inputImage">
          <img :src="getUserProfile()" alt="User image" class="d-inline-block align-text-top border rounded-circle mb-3"
               height="120" loading="lazy" width="120">
        </label>
        <input id="inputImage" accept="image/*" class="d-none" type="file" @change="uploadImage">
        <h3>{{ requestedUser.pseudo }}</h3>
        <hr>
        <p class="lead">Inscrit le {{ getUserTimestamp() }}</p>
        <p class="lead">Rôle : {{ getUserRole() }}</p>

        <div class="form-floating mb-3">
          <input id="floatingBio" ref="bio" v-model="user.bio" class="form-control">
          <label for="floatingBio">Biographie</label>
        </div>

        <button class="btn btn-primary mb-2" @click="edit">Modifier</button>
      </div>
      <div v-else>
        <img :src="getUserProfile()" alt="User image" class="d-inline-block align-text-top border rounded-circle mb-3"
             height="120" loading="lazy" width="120">
        <h3>{{ requestedUser.pseudo }}</h3>
        <hr>
        <p class="lead">Inscrit le {{ getUserTimestamp() }}</p>
        <p class="lead">Rôle : {{ getUserRole() }}</p>
        <p class="lead">{{ getUserBio() }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapActions, mapGetters, mapState} from "vuex";

export default {
  data() {
    return {
      isLoading: true,
      error: null,
      requestedUser: null,
    }
  },
  computed: {
    ...mapGetters(['isConnected']),
    ...mapState(['user']),
  },
  methods: {
    ...mapActions(['setUser']),

    is() {
      return this.isConnected && this.user !== null && this.requestedUser.id === this.user.id
    },

    getUserTimestamp() {
      return new Date(this.requestedUser.timestamp).toLocaleDateString()
    },

    getUserRole() {
      switch (this.requestedUser.role) {
        case 100:
          return 'Administrateur'
        default:
          return 'Membre'
      }
    },

    getUserBio() {
      return (this.requestedUser.bio === null || this.requestedUser.bio.length <= 0) ? 'Aucune biographie pour le moment...' : this.requestedUser.bio;
    },

    getUserProfile() {
      return Utils.getUserProfile(this.requestedUser)
    },

    async setRequestedUser() {
      this.isLoading = true;

      try {
        const response = await fetch(Utils.getLocalFile("php/ziedelth/profile.php?pseudo=" + this.$route.params.pseudo))

        if (response.status === 201) {
          const user = await response.json()

          if (user) {
            this.requestedUser = user;
            this.error = null;
          } else {
            this.requestedUser = null;
            this.error = 'Aucun utilisateur trouvé';
          }
        } else {
          this.requestedUser = null;
          this.error = response.statusText;
        }
      } catch (exception) {
        this.requestedUser = null;
        this.error = exception;
      }

      this.isLoading = false;
    },

    async uploadImage(event) {
      const file = event.target.files[0];

      const formData = new FormData();
      formData.append('file', file);
      formData.append('token', this.user.token);

      try {
        const response = await fetch(Utils.getLocalFile("php/ziedelth/edit_profile.php?type=1"), {
          method: 'POST',
          body: formData
        })

        const json = await response.json();

        if (response.status === 201) {
          this.requestedUser = json;
          this.setUser(json)
        } else
          this.error = json.error;
      } catch (exception) {
        this.error = exception;
      }
    },

    async edit() {
      try {
        const response = await fetch(Utils.getLocalFile("php/ziedelth/edit_profile.php"), {
          method: 'POST',
          body: JSON.stringify({token: this.user.token, bio: this.user.bio})
        })

        const json = await response.json();

        if (response.status === 201) {
          this.requestedUser = json;
          this.setUser(json)
        } else
          this.error = json.error;
      } catch (exception) {
        this.error = exception;
      }
    }
  },
  mounted() {
    this.setRequestedUser()
  }
}
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
<template>
  <div v-else class="text-center">
    <div v-if="error !== null" class="container">
      <p class="alert-danger text-danger rounded fw-bold p-2">{{ error }}</p>
    </div>

    <div v-if="user !== null" class="container">
      <img :src="getUserProfile()" alt="User image" class="d-inline-block align-text-top border rounded-circle mb-3"
           height="120" loading="lazy" width="120">
      <h3>{{ user.pseudo }}</h3>
      <hr>
      <p class="lead">Inscrit le {{ getUserTimestamp() }}</p>
      <p class="lead">Rôle : {{ getUserRole() }}</p>
      <p class="lead">{{ getUserBio() }}</p>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

export default {
  data() {
    return {
      isLoading: true,
      error: null,
      user: null,
    }
  },
  methods: {
    getUserTimestamp() {
      const date = new Date(this.user.timestamp)
      return date.toLocaleDateString()
    },

    getUserRole() {
      switch (this.user.role) {
        case 100:
          return 'Administrateur'
        default:
          return 'Membre'
      }
    },

    getUserBio() {
      return (this.user.bio === null || this.user.bio.length <= 0) ? 'Aucune biographie pour le moment...' : this.user.bio;
    },

    getUserProfile() {
      return Utils.getUserProfile(this.user)
    },
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/ziedelth/profile.php?pseudo=" + this.$route.params.pseudo))

      console.log(response)

      if (response.status === 201) {
        const user = await response.json()

        if (user) {
          this.user = user;
          this.error = null;
        } else {
          this.user = null;
          this.error = 'Aucun utilisateur trouvé';
        }
      } else {
        this.user = null;
        this.error = response.statusText;
      }
    } catch (exception) {
      this.user = null;
      this.error = exception;
    }

    this.isLoading = false;
  }
}
</script>
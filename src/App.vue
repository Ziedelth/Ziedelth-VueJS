<template>
  <div>
    <Navbar/>

    <main class="my-3 vertical-center">
      <div class="container-fluid">

        <div v-if="isLoading" class="spinner-border my-2" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>

        <div v-else>
          <div v-if="maintenance === false">
            <router-view/>
          </div>

          <h3 v-else class="alert-danger text-danger text-center fw-bold p-3 rounded">Serveur en maintenance</h3>
        </div>
      </div>
    </main>

    <Footer/>
  </div>
</template>

<script>
import {mapActions, mapGetters, mapState} from "vuex";
import Utils from "@/utils";

const Navbar = () => import("@/components/Navbar");
const Footer = () => import("@/components/Footer");

export default {
  components: {Footer, Navbar},
  methods: {
    ...mapActions(['setUser']),
  },
  computed: {
    ...mapState(['user']),
    ...mapGetters(['isConnected'])
  },
  data() {
    return {
      isLoading: true,
      maintenance: true,
    }
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/ziedelth/maintenance.php"))

      console.log(response)
      const json = await response.json();
      console.log(json)

      if (response.status === 201) {
        this.maintenance = json.maintenance;

        if (!this.maintenance) {
          try {
            this.$session.start()

            if (!this.isConnected && this.$session.has('token')) {
              const response = await fetch(Utils.getLocalFile("php/ziedelth/login.php"), {
                method: 'POST',
                body: JSON.stringify({token: this.$session.get('token')})
              })

              console.log(response)
              const json = await response.json();
              console.log(json)

              if (response.status === 201) {
                this.success = "Vous êtes maintenant connecté !";

                this.$session.start()
                this.$session.set('token', json.token)
                this.setUser(json)
              }
            }
          } catch (exception) {
          }
        }
      } else
        this.maintenance = true;
    } catch (exception) {
      this.maintenance = true;
    }

    this.isLoading = false;
  }
}
</script>

<style>
.vertical-center {
  min-height: 83.5vh;

  display: flex;
  align-items: center;
}
</style>
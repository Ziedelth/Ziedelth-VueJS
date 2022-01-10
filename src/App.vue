<template>
  <div>
    <Navbar/>

    <main class="my-3 vertical-center">
      <div class="container-fluid">
        <router-view/>
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
  async mounted() {
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
}
</script>

<style>
.vertical-center {
  min-height: 83.5vh;

  display: flex;
  align-items: center;
}
</style>
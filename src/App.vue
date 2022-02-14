<template>
  <div class="bg-full-dark text-white">
    <Navbar />

    <main class="my-3 vertical-center text-center w-100">
      <div class="container-fluid">
        <router-view/>
      </div>
    </main>

    <Footer/>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapGetters, mapState} from "vuex";

const Navbar = () => import("@/components/Navbar");
const Footer = () => import("@/components/Footer");

export default {
  components: {Footer, Navbar},
  computed: {
    ...mapState(['user'])
  },
  methods: {
    ...mapGetters(['isLogin'])
  },
  async mounted() {
    this.$session.start()

    if (this.isLogin())
      return

    if (!this.$session.has('token'))
      return

    try {
      const response = await fetch(Utils.getLocalFile("php/v1/get_user.php"), {
        method: "POST",
        body: JSON.stringify({ token: this.$session.get('token') })
      })

      const json = await response.json()
      console.log(response.statusText)

      if (response.status !== 200) {
        this.error = `${json.error}`
        return
      }

      await this.$store.dispatch('setUser', json)
    } catch (exception) {
      // this.error = `${exception}`
    }
  }
}
</script>

<style>
.bg-full-dark {
  background-color: black;
}

.link-color {
  text-decoration: none;
  color: #ad090a;
  font-weight: 700;
}

.link-color:hover {
  color: #5a1221;
}

.border-color {
  border: #ad090a 1px solid;
}

.vertical-center {
  min-height: 83.5vh;

  display: flex;
  align-items: center;
}
</style>
<template>
  <div class="bg-full-dark text-white">
    <Navbar/>

    <main class="my-3 vertical-center text-center w-100">
      <div class="container-fluid">
        <router-view :key="$route.fullPath"/>
      </div>
    </main>

    <Footer/>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapGetters, mapState} from "vuex";

const Navbar = () => import("@/components/NavbarComponent");
const Footer = () => import("@/components/FooterComponent");

export default {
  components: {Footer, Navbar},
  computed: {
    ...mapState(['user'])
  },
  methods: {
    ...mapGetters(['isLogin'])
  },
  async mounted() {
    await Utils.get(`api/v1/countries`, 200, (success) => {
      console.log(success[0])

      this.$store.dispatch('setCountries', success)
      this.$store.dispatch('setCurrentCountry', success[0])
    }, (failed) => null)

    this.$session.start()

    if (this.isLogin())
      return

    if (!this.$session.has('token'))
      return

    await Utils.post(`php/v1/member/get_user.php`, JSON.stringify({token: this.$session.get('token')}), 200, (success) => {
      this.$store.dispatch('setToken', this.$session.get('token'))
      this.$store.dispatch('setUser', success)
    }, (failed) => {
    })
  }
}
</script>

<style>
.bg-full-dark {
  background-color: black;
}

.link-color {
  text-decoration: none;
  color: #f6a65f;
  font-weight: 700;
}

.link-color:hover {
  color: #f1a60b;
}

.border-color {
  border: #f6a65f 1px solid;
}

.vertical-center {
  min-height: 83.5vh;

  display: flex;
  align-items: center;
}
</style>
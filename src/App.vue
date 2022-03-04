<template>
  <div class="bg-full-dark text-white" style="min-height: 100vh">
    <div v-if="isLoading" class="vertical-center text-center w-100">
      <div class="container-fluid">
        <LoadingComponent is-loading />
      </div>
    </div>

    <div v-else>
      <Navbar/>

      <main class="my-3 vertical-center text-center w-100">
        <div class="container-fluid">
          <router-view :key="$route.fullPath"/>
        </div>
      </main>

      <Footer/>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapGetters, mapState} from "vuex";
import LoadingComponent from "@/components/LoadingComponent";

const Navbar = () => import("@/components/NavbarComponent");
const Footer = () => import("@/components/FooterComponent");

export default {
  components: {LoadingComponent, Footer, Navbar},
  computed: {
    ...mapState(['user'])
  },
  methods: {
    ...mapGetters(['isLogin'])
  },
  data() {
    return {
      isLoading: true,
    }
  },
  async mounted() {
    this.isLoading = true

    await Utils.get(`api/v1/countries`, 200, (success) => {
      this.$store.dispatch('setCountries', success)
      this.$store.dispatch('setCurrentCountry', success[0])
    }, (failed) => null)

    this.isLoading = false
    this.$session.start()

    if (this.isLogin() || !this.$session.has('token'))
      return

    await Utils.post(`php/v1/member/get_user.php`, JSON.stringify({token: this.$session.get('token')}), 200, (success) => {
      this.$store.dispatch('setToken', this.$session.get('token'))
      this.$store.dispatch('setUser', success)
    }, (failed) => null)
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
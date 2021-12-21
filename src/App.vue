<template>
  <div>
    <Navbar/>

    <main class="my-3">
      <div class="container-fluid">
        <router-view/>
      </div>
    </main>

    <Footer/>
  </div>
</template>

<script>
const Navbar = () => import("@/components/Navbar");
const Footer = () => import("@/components/Footer");

export default {
  components: {Footer, Navbar}
}
</script>
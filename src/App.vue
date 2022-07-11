<template>
  <div class="bg-full-dark text-white" style="min-height: 100vh">
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
const Navbar = () => import("@/components/NavbarComponent");
const Footer = () => import("@/components/FooterComponent");

export default {
  components: {Footer, Navbar},
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
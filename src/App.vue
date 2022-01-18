<template>
  <div class="bg-full-dark text-white">
    <Navbar/>

    <main class="my-3 vertical-center text-center w-100">
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
  components: {Footer, Navbar},
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
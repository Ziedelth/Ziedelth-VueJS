<template>
  <div>
    <Navbar/>

    <main class="my-3">
      <b-container>
        <router-view/>
      </b-container>
    </main>

    <Footer/>
  </div>
</template>

<script>
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

export default {
  components: {Footer, Navbar}
}
</script>
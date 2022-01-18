<template>
  <footer>
    <div class="container-fluid mt-3 p-3 bg-dark">
      <div class="row">
        <div class="col-6 text-start">
          <p class="text-white-50 fw-bold">&copy; 2021 - {{ getCurrentYear() }} Ziedelth.fr</p>
        </div>
        <div class="col-6 text-end">
          <router-link class="text-white text-decoration-none" to="/privacy">Conditions générales d'utilisations
          </router-link>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  methods: {
    getCurrentYear() {
      return new Date().getFullYear()
    }
  }
}
</script>
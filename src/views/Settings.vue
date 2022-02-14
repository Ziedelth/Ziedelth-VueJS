<template>
  <div class="container-fluid">
    <h3>Paramètres</h3>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  methods: {
    ...mapGetters(['isLogin'])
  },
  mounted() {
    if (!this.isLogin()) {
      this.$router.push('/login')
    }
  }
}
</script>
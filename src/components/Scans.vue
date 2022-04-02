<template>
  <div class="row row-cols-lg-4 g-3 d-flex justify-content-center text-center mb-3">
    <div v-for="scan in scans" class="col-lg">
      <ScanComponent :scan="scan"/>
    </div>
  </div>
</template>

<script>
const ScanComponent = () => import("@/components/ScanComponent");

export default {
  name: 'Scans',
  components: {
    ScanComponent
  },
  props: {
    scans: {}
  }
}
</script>
<template>
  <div class="container-fluid w-100">
    <div v-if="!isLoading" class="row row-cols-lg-1 g-3 d-flex justify-content-center text-center">
      <div class="col">
        <Chart :data="globalData"/>
      </div>

      <div class="col">
        <Chart :data="durationData"/>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from "@/components/Chart";
import Utils from "@/utils";

export default {
  components: {Chart},
  data() {
    return {
      isLoading: true,
      globalData: null,
      durationData: null,
    }
  },
  async mounted() {
    this.isLoading = true;

    await Utils.get(`api/v1/statistics`, (success) => {
      this.globalData = {
        labels: success.map((item) => item.date),
        datasets: [
          {
            label: "Animes",
            borderColor: "#FC2525",
            borderWidth: 2,
            data: success.map((item) => item.animes),
          },
          {
            label: "Episodes",
            borderColor: "#fc8925",
            borderWidth: 2,
            data: success.map((item) => item.episodes),
          },
          {
            label: "Scans",
            borderColor: "#b6631e",
            borderWidth: 2,
            data: success.map((item) => item.scans),
          },
        ]
      };

      this.durationData = {
        labels: success.map((item) => item.date),
        datasets: [
          {
            label: "Durée (en heures)",
            borderColor: "#2589fc",
            borderWidth: 2,
            data: success.map((item) => item.duration / 3600),
          },
        ]
      };
    }, (failed) => null)

    this.isLoading = false;
  }
}
</script>
<template>
  <div class="container-fluid">
    <LoadingComponent :is-loading="isLoading" />

    <div v-if="!isLoading">
      <h5>Données globales au mois</h5>
      <Chart :data="globalDataMonth"/>
    </div>
  </div>
</template>

<script>
const LoadingComponent = () => import("@/components/LoadingComponent");
const Chart = () => import("@/components/Chart");
import Utils from "@/utils";

export default {
  components: {LoadingComponent, Chart},
  data() {
    return {
      isLoading: true,
      globalDataMonth: null,
    }
  },
  async mounted() {
    this.isLoading = true;

    await Utils.get(`api/v1/statistics/30`, (success) => {
      const globalDataset = [
        {
          label: "Total",
          borderColor: "#ffffff",
          borderWidth: 2,
          data: success.map((item) => item.episodes + item.scans),
        },
      ];

      success.forEach(data => {
        data.platforms.forEach(item => {
          if (globalDataset.some(data => data.label === item.name)) {
            globalDataset.filter(data => data.label === item.name).forEach(data => {
              data.data.push(item.episodes + item.scans);
            });
          } else {
            globalDataset.push({
              label: item.name,
              borderColor: "#" + item.color.toString(16).padStart(6, "0"),
              borderWidth: 2,
              data: [item.episodes + item.scans],
            });
          }
        });
      });

      this.globalDataMonth = {
        labels: success.map((item) => item.date),
        datasets: globalDataset
      };
    }, (failed) => null)

    this.isLoading = false;
  }
}
</script>
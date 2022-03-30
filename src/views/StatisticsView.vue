<template>
  <div class="container-fluid">
    <LoadingComponent :is-loading="isLoading" />

    <div v-if="!isLoading">
      <h5>Épisodes</h5>

      <div class="row row-cols-lg-1 g-3 d-flex justify-content-center text-center">
        <div class="col">
          <h5>Données globales au mois</h5>
          <Chart :data="globalDataMonth"/>
        </div>

        <div class="col">
          <h5>Durée globale au mois (en heures)</h5>
          <Chart :data="durationDataMonth"/>
        </div>
      </div>
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
      durationDataMonth: null,
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
          data: success.map((item) => item.episodes),
        },
      ];
      const durationDataset = [
        {
          label: "Total",
          borderColor: "#ffffff",
          borderWidth: 2,
          data: success.map((item) => item.duration / 3600),
        },
      ];

      success.forEach(data => {
        data.platforms_count.forEach(item => {
          if (globalDataset.some(data => data.label === item.name)) {
            globalDataset.filter(data => data.label === item.name).forEach(data => {
              data.data.push(item.count);
            });
          } else {
            globalDataset.push({
              label: item.name,
              borderColor: "#" + item.color.toString(16).padStart(6, "0"),
              borderWidth: 2,
              data: [item.count],
            });
          }
        });

        data.platforms_duration.forEach(item => {
          if (durationDataset.some(data => data.label === item.name)) {
            durationDataset.filter(data => data.label === item.name).forEach(data => {
              data.data.push(item.duration / 3600);
            });
          } else {
            durationDataset.push({
              label: item.name,
              borderColor: "#" + item.color.toString(16).padStart(6, "0"),
              borderWidth: 2,
              data: [item.duration / 3600],
            });
          }
        });
      });

      this.globalDataMonth = {
        labels: success.map((item) => item.date),
        datasets: globalDataset
      };

      this.durationDataMonth = {
        labels: success.map((item) => item.date),
        datasets: durationDataset
      };
    }, (failed) => null)

    this.isLoading = false;
  }
}
</script>
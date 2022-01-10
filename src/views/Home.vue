<template>
  <div>
    <div v-if="isLoading" class="spinner-border my-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>

    <div v-else>
      <div v-if="maintenance === false">
        <Jais/>

        <hr>

        <div class="text-center">
          <h3 class="fw-bold my-3">Prochainement</h3>

          <div class="row g-3">
            <div class="col">
              <img alt="Albion brand" class="img-fluid border rounded-circle shadow" height="120" loading="lazy"
                   src="images/albion.jpg" width="120">
              <p class="mt-2 lead fw-bold">Albion</p>
            </div>

            <div class="col">
              <img alt="Ddraig brand" class="img-fluid border rounded-circle shadow" height="120" loading="lazy"
                   src="images/ddraig.jpg" width="120">
              <p class="mt-2 lead fw-bold">Ddraig</p>
            </div>

            <div class="col">
              <img alt="Ophis brand" class="img-fluid border rounded-circle shadow" height="120" loading="lazy"
                   src="images/ophis.jpg" width="120">
              <p class="mt-2 lead fw-bold">Ophis</p>
            </div>
          </div>
        </div>
      </div>

      <h3 v-else class="alert-danger text-danger text-center fw-bold p-3 rounded">Serveur en maintenance</h3>
    </div>
  </div>
</template>
<script>
import Utils from "@/utils";

const Jais = () => import("@/components/Jais");

export default {
  components: {Jais},
  data() {
    return {
      isLoading: true,
      maintenance: true,
    }
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/ziedelth/maintenance.php"))

      console.log(response)
      const json = await response.json();
      console.log(json)

      if (response.status === 201)
        this.maintenance = json.maintenance;
      else
        this.maintenance = true;
    } catch (exception) {
      this.maintenance = true;
    }

    this.isLoading = false;
  }
}
</script>
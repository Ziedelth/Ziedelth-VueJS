<template>
  <div>
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
</template>
<script>
const Jais = () => import("@/components/Jais");

export default {
  components: {Jais},
}
</script>
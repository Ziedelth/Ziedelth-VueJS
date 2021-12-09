<template>
  <div>
    <Jais/>

    <hr>

    <div class="text-center">
      <h3 class="fw-bold my-3">Prochainement</h3>

      <b-row cols="3">
        <b-col>
          <img alt="Albion brand" class="img-fluid border rounded-circle shadow" height="120" loading="lazy"
               src="images/albion.jpg" width="120">
          <p class="mt-2 lead fw-bold">Albion</p>
        </b-col>

        <b-col>
          <img alt="Ddraig brand" class="img-fluid border rounded-circle shadow" height="120" loading="lazy"
               src="images/ddraig.jpg" width="120">
          <p class="mt-2 lead fw-bold">Ddraig</p>
        </b-col>

        <b-col>
          <img alt="Ophis brand" class="img-fluid border rounded-circle shadow" height="120" loading="lazy"
               src="images/ophis.jpg" width="120">
          <p class="mt-2 lead fw-bold">Ophis</p>
        </b-col>
      </b-row>
    </div>
  </div>
</template>
<script>
import Jais from "@/components/Jais";

export default {
  components: {Jais},
}
</script>
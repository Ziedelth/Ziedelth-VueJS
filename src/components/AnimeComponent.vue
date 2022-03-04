<template>
  <div class="text-truncate">
    <router-link :to="`/anime/${id}`" class="card-title fw-bold link-color">{{
        name
      }}
    </router-link>
  </div>
</template>
<script>
export default {
  name: 'AnimeComponent',
  props: {
    id: {
      type: Number,
      required: true
    },
    name: {
      type: String,
      required: true
    }
  }
}
</script>
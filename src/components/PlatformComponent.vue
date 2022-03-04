<template>
  <a :href="url" target="_blank" class="text-decoration-none text-white">
    <img :src="image" alt="Platform image" class="platform-thumbnail"/>
    {{ name }}
  </a>
</template>

<script>
export default {
  name: 'PlatformComponent',
  props: {
    url: {
      type: String,
      required: true
    },
    image: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    }
  }
}
</script>
<style scoped>
.platform-thumbnail {
  width: 3vh;
  height: 3vh;
  border-radius: 50%;
  margin: .25rem;
}
</style>
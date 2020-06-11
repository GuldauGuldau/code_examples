<template>
  <section class="bl-gallery1">
    <base-gallery
      :images="fields.items"
      key-url="image"
    ></base-gallery>
  </section>
</template>

<script>
  export default {
    props: {
      fields: {
        type: Object,
        default: null
      },
    },
  }
</script>

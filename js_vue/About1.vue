<template>
  <section class="ln-info">
    <div class="ln-container">
      <div class="pure-g pure-row">
        <div class="pure-u-1 pure-u-md-14-24 p10 ln-info-col1">
          <div class="ln-info-content">
            <h1 class="ln-info-title" v-html="fields.title"></h1>
            <div class="ln-info-text" v-html="fields.text"></div>
          </div>
        </div>
        <div class="pure-u-1 pure-u-md-10-24 p10 ln-info-col2">
          <img :src="fields.image" alt="" style="width:100%">
        </div>
      </div>
    </div>
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

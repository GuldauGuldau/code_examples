<template>
  <section
  class="bl-banner1"
  :style="{'background-image':'url(' + fields.image + ')'}"
  >
    <div class="bl-banner1-body">
      <div class="ln-container">
        <h2 class="bl-banner1-title" v-html="fields.title"></h2>
        <div class="bl-banner1-text" v-html="fields.text"></div>
        <div class="bl-banner1-footer">
          <bid-btn
          :company-id="companyId"
          template="action-btn"
          >
        </bid-btn>
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
    computed: {
      companyId() {
        return this.$parent.companyId;
      }
    }
  }
</script>

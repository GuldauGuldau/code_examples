<template>
  <div class="ci-cm-contact-item">
    <template v-if="contact.contact_group == 'social_network'">
      <a :href="contact.value" target="_blank">
        <img :src="'/storage/static/contact-icon/' + contact.contact_type +'.svg'" alt="">
        <span>{{ alias[contact.contact_type] }}</span>
      </a>
    </template>

    <template v-else>
        <img :src="'/storage/static/contact-icon/' + contact.contact_type +'.svg'" alt="">
        <template v-if="contact.contact_type == 'website'">
          <a :href="contact.value" target="_blank" class="ci-cm-link">{{ sliceHttp(contact.value) }}</a>
        </template>
        <template v-else >
          <span>{{ contact.value }}</span>
        </template>

    </template>
  </div>
</template>
<script>
  export default {
    props: {
      contact: {
        type: Object
      },
    },
    data: function () {
      return {
        alias: {
          'vkontakte' : 'Вконтакте',
          'facebook' : 'Facebook',
          'instagram' : 'Instagram',
          'odnoklassniki' : 'Odnoklassniki',
          'youtube' : 'Youtube',
          'twitter' : 'Twitter',
          'pinterest' : 'Pinterest',
          'houzze' : 'Houzze',
          'viber' : 'Viber',
          'telegram' : 'Telegram',
          'whatsapp' : 'Whatsapp',
          'skype' : 'Skype',
        }
      }
    },
    methods: {
      sliceHttp(value) {
        if(value.includes("http://")) {
          value = value.slice(7);
        }
        if(value.includes("https://")) {
          value = value.slice(8);
        }

        if(value.includes("www.")) {
          return value.replace("www.", "");
        }
        return value;
      },
    },
  }
</script>

<template>
    <section class="bl-contact">
      <div class="ln-container">
        <h2 class="ln-base-title-lg">Контакты</h2>

        <div class="pure-g pure-row">
          <template v-if="addresses.length > 0">
            <template v-if="Object.entries(this.contacts).length > 0">
              <div class="pure-u-1 pure-u-sm-24-24 pure-u-md-14-24 pure-u-xl-10-24 p10">
                <div class="bl-contact-group" v-for="(contact_group, index) in contacts">
                  <h4 class="bl-contact-title">{{ index }}</h4>
                  <div class="bl-contact-body">
                      <div class="pure-g pure-row">
                        <div
                          class="pure-u-12-24 pure-u-sm-12-24 p10-lr"
                          v-for="contact in contact_group"
                          :key="contact.id">

                          <contact1-item
                            :contact="contact">
                          </contact1-item>

                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </template>

              <div class="pure-u-1 p10" :class="[mapClass]">
                <div class='bl-contact-map'>
                  <base-map
                    :coords="coords"
                    :height="300"
                    :popups="popups"
                    :isopenpopup="false"
                  ></base-map>
                </div>
            </div>
          </template>

          <template v-else>
              <div class="pure-u-1 pure-u-md-8-24 p10" v-for="(contact_group, index) in contacts">
                <div class="bl-contact-group">
                  <h4 class="bl-contact-title">{{ index }}</h4>
                  <div class="bl-contact-body">
                    <contact1-item
                      v-for="contact in contact_group"
                      :key="contact.id"
                      :contact="contact">
                    </contact1-item>
                  </div>
                </div>
              </div>
          </template>

        </div>
      </div>
    </section>
</template>

<script>
import Contact1Item from './Contact1Item';
  export default {
    props: {
      fields: {
        type: Object,
        default: null
      },
      data: {
        type: Object,
        default: null,
      },
    },
    components: {
      Contact1Item,
    },
    data: function () {
      return {
        contacts: this.data.contacts,
        addresses: this.data.addresses,
      }
    },
    computed: {
      coords() {
        let out = [];
        for(let i = 0; i<this.addresses.length; i++) {
          out.push([this.addresses[i]['lat'], this.addresses[i]['lon']]);
        }
        return out;
      },
      popups() {
        let popups = [];
        if(this.addresses.length > 0) {
          for(let i = 0; i<this.addresses.length; i++) {
            popups.push('<div class="bl-contact-popup-title">' + this.addresses[i]['address'] + '</div><div class="bl-contact-popup-text">' + this.addresses[i]['description'] + '</div>');
          }
        }
        return popups;
      },
      mapClass() {
        return Object.entries(this.contacts).length > 0 ? 'pure-u-sm-24-24 pure-u-md-10-24 pure-u-xl-14-24' : 'pure-u-md-24-24';
      }
    }
  }
</script>

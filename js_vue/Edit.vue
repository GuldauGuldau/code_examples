<template>
  <div>
    <div  v-if="section != null">
      <div class="bl-item bl-default">
        <label class="bl-item-label">Название пункта меню</label>
        <input type="text" v-model="section.menu_name" class="bl-input" placeholder="Название пункта меню">
      </div>
      <template v-if="section.fields.items">
        <div v-if="section.icon == 'gallery'">
          <div class="bl-items-gallery">
            <div
              v-for="(item, key) in section.fields.items"
              class="bl-item-file"
            >
              <upload-file
                :image="item.image"
                template="gallery"
                @update-field="updateItems(key, 'image', $event)"
              ></upload-file>
              <div class="bl-item-file-delete" @click="deleteItem(key)">x</div>
            </div>
          </div>
          <upload-btn
            @loaded="addImage(section, $event)"
          ></upload-btn>
        </div>

        <div v-else>
        <div
          class="bl-items"
          v-for="(item, key) in section.fields.items"
        >
          <div class="bl-delete" @click="deleteItem(key)">Удалить</div>
          <div
            v-for="(itemValue, name, index) in item"
            class="bl-item"
          >
            <template v-if="section.scheme[name] == 'string'">
              <input type="text" v-model="item[name]" class="bl-input" placeholder="Введите текст">
            </template>

            <template v-if="section.scheme[name] == 'text'">
              <vue-trix
              v-model="item[name]"
              placeholder="Введите текст"
              class="ln-editor"/>
            </template>

            <template v-if="section.scheme[name] == 'icon'">
              <select-icon
                :icons="icons"
                :icon="itemValue"
                @update-item="updateItems(key, name, $event)"
              ></select-icon>
            </template>
          </div>
        </div>
        <div class="bl-btn" @click="addItem(section)">Добавить элемент</div>
      </div>
      </template>

      <template v-else-if="section.fields.source">
        <div v-if="source" class="bl-source">
          <div v-for="(source_item, key) in source"class="bl-source-item">
            <div v-if="source_item.length == 0" class="bl-text">
              <span v-html="emptyLabel[key]"></span>
            </div>
            <div v-else v-html="sourceText[key]"></div>
          </div>

          <template v-for="source in section.fields.source">
            <template v-if="source.limit">
              <div class="bl-group">
                <label class="bl-label">{{ sourceLabel['limit.' + source.name] }}</label>
                <input
                  type="number"
                  v-model="source.limit"
                  class="bl-input"
                  placeholder="Число элементов"
                  min="1">
              </div>
            </template>
          </template>
        </div>

        <div class="bl-item bl-default">
          <template v-if="section.fields.text">
            <vue-trix
            v-model="section.fields.text"
            placeholder="Введите текст"
            class="ln-editor"/>
          </template>
        </div>
      </template>

      <template v-else>
        <div class="bl-item bl-default" v-for="(value, name) in section.fields">
          <template v-if="section.scheme[name] == 'file'">
            <upload-file
              :image="section.fields[name]"
              @update-field="updateField(name, $event)"
            ></upload-file>
          </template>

          <template v-if="section.scheme[name] == 'string'">
            <input type="text" v-model="section.fields[name]" class="bl-input" placeholder="Введите текст">
          </template>

          <template v-if="section.scheme[name] == 'text'">
            <vue-trix
            v-model="section.fields[name]"
            placeholder="Введите текст"
            class="ln-editor ln-editor-hide-toolbar"/>
          </template>

          <template v-if="section.scheme[name] == 'editor'">
            <vue-trix
            v-model="section.fields[name]"
            placeholder="Введите текст"
            @trix-attachment-add="handleAttachmentChanges"
            class="ln-editor"
            />
          </template>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    section: {
      type: Object,
      default: null
    },
    data: {
      type: Object,
      default: null
    },
  },
  data: function () {
    return {
      icons: null,
      emptyLabel: {
        services: 'У вас нет ни одной услуги. Добавить услуги можно <a href="/account/service" target="_blank">в личном кабинете</a>',
        projects: 'У вас нет ни одного проекта. Добавить проект можно <a href="/account/project" target="_blank">в личном кабинете</a>',
        contacts: 'У вас нет ни одного контакта. Добавить контакт можно <a href="/account/contact" target="_blank">в личном кабинете</a>',
        addresses: 'У вас нет ни одного адреса. Добавить адрес можно <a href="/account/branch" target="_blank">в личном кабинете</a>',
        sales: 'У вас нет ни одной действующей акции. Управлять акциями можно <a href="/account/branch" target="_blank">в личном кабинете</a>',
      },
      sourceText: {
        services: 'Вы можете добавлять, редактировать или удалять услуги <a href="/account/service" target="_blank">в личном кабинете</a>',
        projects: 'Вы можете добавлять, редактировать или удалять пректы <a href="/account/project"  target="_blank">в личном кабинете</a>',
        contacts: 'Вы можете добавлять, редактировать или удалять контакты <a href="/account/contact" target="_blank">в личном кабинете</a>',
        addresses: 'Вы можете добавлять, редактировать или удалять адреса <a href="/account/branch"  target="_blank">в личном кабинете</a>',
        sales: 'Вы можете добавлять, редактировать или удалять акции <a href="/account/sales"  target="_blank">в личном кабинете</a>',
      },
      sourceLabel: {
        'limit.projects' : 'Количество отображаемых проектов',
        'limit.services' : 'Количество отображаемых услуг'
      },
    }
  },
  computed: {
    source() {
      if(this.section.fields.source) {
        let out = {};
        for(let i=0; i<this.section.fields.source.length; i++) {
          out[this.section.fields.source[i]['relation_name']] = this.data[this.section.fields.source[i]['relation_name']];
        }
        return out;
      }
      return null;
    },
  },
  methods: {
    handleAttachmentChanges(event) {
      let file = event.attachment.file;
      let formData = new FormData();
      if (file.type.substr(0, 6) === 'image/') {
        formData.append('image', file);
      }
      axios
      .post('/api/files/upload-template', formData, {headers: {'Content-Type': 'multipart/form-data'}})
      .then(response => {
        let attributes = {
          url: response.data,
          href: response.data
        };
        event.attachment.setAttributes(attributes);
      }).catch(error => {
        if (error.response) {
          console.log(error.response);
        }
      });
    },
    updateItems(key, name, value) {
      Vue.set(this.section.fields.items[key], name, value);
    },
    updateField(name, value) {
      Vue.set(this.section.fields, name, value);
    },
    addItem(section) {
      var item = {};
      for (var key in this.section.scheme) {
        item[key] = '';
      }
      this.section.fields.items.push(item);
    },
    addImage(section, value) {
      var item = {};
      for (var key in this.section.scheme) {
        item[key] = value;
      }
      this.section.fields.items.push(item);
    },
    deleteItem(key) {
      this.section.fields.items.splice(key, 1);
    }
  },
  mounted() {
    axios
    .get('/api/files/feature-icon')
    .then(response => {
      this.icons = response.data;
    }).catch(error => {
      if (error.response) {
        console.log(error.response);
      }
    });
  }
}
</script>

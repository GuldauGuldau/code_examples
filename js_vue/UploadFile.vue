<template>
  <div class="bl-file-block">
    <span class="bl-upload-info">Макс. размер изображения 5мб</span>
    <template v-if="template == 'gallery'">
      <label class="bl-file bl-file-min">
        <input type="file" @change="handleFilesUpload($event)">
        <div v-if="image" class="bl-image bl-image-bg" :style="{'background-image':'url(' + image + ')'}">
        </div>
      </label>
    </template>
    <template v-else>
      <label class="bl-file">
        <input type="file" @change="handleFilesUpload($event)">
        <div v-if="image" class="bl-image">
          <img :src="image" alt="">
        </div>
        <div class="bl-btn">Выбрать файл</div>
      </label>
    </template>
    <div v-if="isLoading" class="bl-file-load">Загрузка..</div>
  </div>

</template>

<script type="text/javascript">
export default {
  props: {
    image: {
      type: String,
      default: null
    },
    template: {
      type: String,
      default: ''
    },
  },
  data() {
    return {
      myOptions: null,
      show: false,
      isLoading: false
    };
  },
  methods: {
    handleFilesUpload(event) {
        let files =  event.target.files;
        let formData = new FormData();
        if (files[0].type.substr(0, 6) === 'image/') {
          formData.append('image', files[0]);
        }

        if(this.image) {
          formData.append('old', this.image);
        }
        this.isLoading = true;
        axios
        .post('/api/files/upload-template', formData, {headers: {'Content-Type': 'multipart/form-data'}})
        .then(response => {
          this.$emit("update-field", response.data);
          this.isLoading = false;
        }).catch(error => {
          if (error.response) {
            console.log(error.response);
          }
        });

    },
  },

};
</script>

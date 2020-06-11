<template>
  <div class="bl-file-block">
      <div class="bl-file-error" v-if="isError">
        Произошла ошибка при загрузке. Недопустимый размер или формат файла.
      </div>
      <div v-if="isLoading" class="bl-btn">Загрузка..</div>
      <label v-else class="bl-file">
        <input type="file" @change="handleFilesUpload($event)">
        <div class="bl-btn">Добавить файл</span></div>
      </label>

      <div class="bl-file-info">
        Макс. размер файла 5Мб. Допустимые форматы: .jpg, .png, .webp, .wbmp.
      </div>

  </div>
</template>

<script type="text/javascript">
export default {
  data() {
    return {
      myOptions: null,
      show: false,
      isLoading: false,
      isError: false
    };
  },
  methods: {
    handleFilesUpload(event) {
        let files =  event.target.files;
        let formData = new FormData();
        if (files[0].type.substr(0, 6) === 'image/') {
          formData.append('image', files[0]);
        }
        this.isLoading = true;
        this.isError = false;
        axios
        .post('/api/files/upload-template', formData, {headers: {'Content-Type': 'multipart/form-data'}})
        .then(response => {
          this.$emit("loaded", response.data);
          this.isLoading = false;
        }).catch(error => {
          if (error.response) {
            this.isError = true;
            this.isLoading = false;
            console.log(error.response);
          }
        });

    },
  },

};
</script>

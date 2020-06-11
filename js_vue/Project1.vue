<template>
  <section class="ln-project" >
    <div class="ln-container">
      <div class="ln-js-flex">
        <h2 class="ln-base-title-lg-bold">Наши проекты</h2>
        <a
          v-if="projects.length > 3"
          :href="'/company/' + companyAlias + '/projects'"
          class="ln-btn-default ln-btn-transparent-dark ln-btn-fix-width"
          >Все проекты</a>
      </div>

      <div v-if="dataElements.length > 0">
        <div class="ln-project-list" >
          <template v-for="project in dataElements">
              <div class="ln-project-item" v-if="project.is_show">
                <a :href="'/projects/'+ project.id"
                   class="ln-project-thumb"

                   >
                   <div
                   :style="{'background-image':'url(' + project.thumb + ')'}"
                   class="ln-project-thumb-transform"
                   ></div>
                </a>
                <div class="ln-project-title"><a :href="'/projects/'+ project.id">{{ project.name }}</a></div>

                <div class="ln-project-desc"><a :href="'/projects/'+ project.id">{{ project.str_property }}</a></div>
                <template v-if="project.rubrics.length > 0">
                  <div class="ln-tags" >
                    <template v-for="rubric in project.rubrics">
                        <a :href="'/projects?rubric='+rubric.id" target="_blank" >{{ rubric.slashName }}</a>
                    </template>
                  </div>
                </template>
              </div>
          </template>
        </div>
        <div class="ln-project-action" v-if="projects.length > 3">
          <a :href="'/company/' + companyAlias + '/projects'" class="ln-btn-default ln-btn-transparent-dark ln-btn-fix-width">Все проекты</a>
        </div>
      </div>

      <div v-else >
        <div class="bl-empty-text">
          Не создано ни одного проекта.<br>
          Добавить проект вы можете в <a href="/account/project" target="_blank">личном кабинете</a>
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
    data: {
      type: Object,
      default: null
    },
    companyAlias: {
      type: String,
      default: null
    }
  },
  data: function () {
    return {
      projects: this.data.projects
    }
  },
  computed: {
    limit() {
      return this.fields.source[0].limit;
    },
    dataElements() {
      return this.projects.slice(0, this.limit);
    }
  },
  mounted() {
  }
}
</script>

<template>
  <div>
    <div class="ln-nav-top ln-nav-white ln-nav-company">
      <div class="ln-container">
        <div class="ln-flex">
          <div class="ln-nav-logo">
            <img :src="companyLogo" alt="">
          </div>
          <div class="ln-nav-scroll-wrap">
            <nav class="ln-nav-top-navbar" :class="[navClass]" id="nav-company">
              <template v-for="(section,index) in dataSections">
              <a :href="'#section' + section.id"
                @click.prevent="scrollTo"
                >
                {{ section.menu_name }}
              </a>
              </template>
              <a :href="'/company/' + companyAlias" class="ln-btn-default ln-btn-xs-white ln-btn-action">Назад</a>
            </nav>
          </div>
        </div>
      </div>
    </div>

  <main class="bl" :class="{'pointer-none': hasEvent, 'open': isOpen, 'editor': currentSection }">
    <div v-if="dataSections.length == 0">
      <section class="bl-empty" @click="isOpen=false">
        <div class="ln-container">
          <div class="bl-empty-image">
            <img src="/storage/static/section-preview/banner_1.jpg" alt="">
            <img src="/storage/static/section-preview/about_2.jpg" alt="">
            <img src="/storage/static/section-preview/project_2.jpg" alt="">
          </div>
          <div class="bl-empty-title">
            Конструктор старинцы
          </div>

          <div class="bl-empty-text">
            Что бы добавить секцию, нажмите "+" на панели справа.
          </div>
        </div>
      </section>
    </div>
    <template v-for="(section,index) in dataSections">
      <div class="ln-builder-wrap" :id="'section-'+section.id">
        <div class="ln-builder-panel">
          <div class="ln-builder-action-item ln-builder-action-editbtn"
            @click.stop="editSection(section)"
            v-tooltip.bottom="{content:'Редактировать', delay: 0}"
            >
            <img src="/storage/static/edit.svg">
            <span class="ln-builder-action-item-text">Редактировать</span>
          </div>
          <div class="ln-builder-action-item"
            @click="changeOrder(section,'up')"
            v-tooltip.bottom="{content:'Переместить выше', delay: 0}"
            >
            <img src="/storage/static/arrow-top.svg">
          </div>
          <div class="ln-builder-action-item"
            @click="changeOrder(section,'down')"
            v-tooltip.bottom="{content:'Переместить ниже', delay: 0}"
            >
            <img src="/storage/static/arrow-bottom.svg">
          </div>
          <div class="ln-builder-action-item"
            @click="deleteSection(section)"
            v-tooltip.bottom="{content:'Удалить', delay: 0}"
          >
            <img src="/storage/static/delete.svg">
          </div>
        </div>
        <div class="ln-builder-separator">
          <div class="ln-builder-plusbtn" @click="insertSection(section)">
            <span>+</span>&nbsp;&nbsp;Добавить
          </div>
        </div>

        <div :id="'section'+section.id" @click="isOpen=false">
          <component
            v-bind:is="section.name"
            :fields="section.fields"
            :data="data"
            :company-alias="companyAlias"
            :company-name="companyName"
            :menu-name="section.menu_name"
          >
          </component>
        </div>
      </div>
    </template>

    <aside class="ln-builder">
      <div class="ln-builder-content">
        <div class="ln-builder-header">
          <h5 class="ln-builder-title">
            <span v-if="currentSection == null">Конструктор страницы</span>
            <div v-else class="ln-builder-btn" @click="updateAndClose">
              Сохранить и закрыть
            </div>
          </h5>
          <template v-if="currentSection !== null">
            <div class="ln-builder-action">
              <div class="ln-builder-action-item"
                @click="changeOrder(currentSection,'up')"
                v-tooltip.bottom="{content:'Переместить выше', delay: 0}">
                <img src="/storage/static/arrow-top.svg">
              </div>
              <div class="ln-builder-action-item"
                @click="changeOrder(currentSection,'down')"
                v-tooltip.bottom="{content:'Переместить ниже', delay: 0}">
                <img src="/storage/static/arrow-bottom.svg">
              </div>
              <div class="ln-builder-action-item"
                @click="deleteSection(currentSection)"
                v-tooltip.bottom="{content:'Удалить', delay: 0}"
                >
                <img src="/storage/static/delete.svg">
              </div>
            </div>
          </template>
          <div class="ln-builder-back" @click="closeBuilder" v-if="currentSection == null">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.918 8.60651L8.03696 15.4876C7.71647 15.8081 7.71578 16.3225 8.03267 16.6394C8.35176 16.9585 8.86526 16.9544 9.18452 16.6351L16.6351 9.18458L16.6372 9.18243L16.6394 9.18029C16.7982 9.02145 16.8769 8.81445 16.8766 8.60722C16.8761 8.39716 16.7971 8.19046 16.6394 8.03273L16.6372 8.03058L16.6351 8.02843L9.18452 0.577892C8.86403 0.257399 8.34956 0.256708 8.03267 0.573597C7.71357 0.892696 7.7177 1.40619 8.03696 1.72545L14.918 8.60651Z" fill="black"/>
            </svg>
          </div>
        </div>
        <div  class="ln-builder-scroll" v-bar> <!-- el1 -->
          <div>
        <div class="ln-builder-body">
          <template v-if="currentSection == null">
            <div class="ln-builder-templates">
              <div class="ln-builder-templates-item" v-for="tsection in templateSection" @click="addSection(tsection)">
                <div class="ln-builder-templates-title">{{ tsection.lang_label }}</div>
                <div class="ln-builder-templates-cover"
                  :style="{'background-image': 'url(/storage/'+tsection.preview+')'}"
                  >
                  <img
                    src="/storage/static/plus.svg"
                    alt=""
                    class="ln-builder-templates-add">
                </div>

              </div>
            </div>
          </template>
          <template v-else>
            <edit
              :section="currentSection"
              :data="data"
            ></edit>
          </template>
        </div>
      </div>
    </div>
      </div>
    </aside>

    <div class="ln-builder-control" v-if="!isOpen">
      <div class="ln-builder-control-item" @click="openBuilder()" v-tooltip.left="'Добавить блок'">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M16.1282 7.10955H10.8718V1.85325C10.8718 0.832242 10.0399 -0.0185984 9 0.00030918C7.97899 0.00030918 7.14706 0.832242 7.12815 1.87216V7.12846H1.87185C0.85084 7.12846 0 7.96039 0 9.00031C0 9.51081 0.207983 9.9835 0.548319 10.3238C0.888655 10.6642 1.34244 10.8722 1.87185 10.8722H7.12815V16.1285C7.12815 16.639 7.33613 17.1117 7.67647 17.452C8.01681 17.7923 8.47059 18.0003 9 18.0003C10.021 18.0003 10.8529 17.1684 10.8718 16.1285V10.8722H16.1282C17.1492 10.8722 18 10.0402 18 9.00031C17.9811 7.96039 17.1492 7.12846 16.1282 7.10955Z" fill="#16C0D9"/>
        </svg>
      </div>

      <div class="ln-builder-control-scroll">
        <div>
          <div class="ln-builder-control-item"
               v-for="section in dataSections"
               @click="editSection(section)"
               v-tooltip.top="{content:'Редактировать ' + tooltip[section.icon], delay: 0}"
            >
            <img :src="'/storage/static/' + section.icon + '.svg'">
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</template>

<script>
  export default {
    props: {
      sections: {
        type: Array,
        default: null
      },
      data: {
        type: Object,
        default: null
      },
      templateSection: {
        type: Array,
        default: null
      },
      companyAlias: {
        type: String,
        default: null
      },
      companyLogo: {
        type: String,
        default: null
      },
      companyName: {
        type: String,
        default: null
      },
      companyId: {
        type: Number,
        default: null
      }
    },
    data: function () {
        return {
          dataSections: this.sections,
          currentSection: null,
          tooltip: {
            'about'   : 'О нас',
            'feature' : 'Преимущества',
            'service' : 'Услуги',
            'project' : 'Проекты',
            'price'   : 'Стоимость',
            'contact' : 'Контакты и адреса',
            'partner' : 'Партнеры',
            'banner' : 'Обложка',
            'sale' : 'Акции',
            'info' : 'Информация',
            'gallery' : 'Галерея',
            'quote' : 'Цитата',
          },
          hasEvent: false,
          isOpen: false,
          insertBeforeOrder: null
        }
    },
    computed: {
      navClass() {
        if(this.dataSections.length <= 4) {
          return 'ln-nav-top-navbar-start';
        }
        return '';
      }
    },
    methods: {
      addSection(tsection) {
        let addSectionOrder = (this.insertBeforeOrder) ? this.insertBeforeOrder : (this.dataSections.length + 1);
        axios
        .post('/api/builder/attachSection',{
          template_id: this.data.template_id,
          section_name: tsection.name,
          order: addSectionOrder
        })
        .then(response => {
          this.dataSections = response.data;
          let scrollTo = this.dataSections.filter(item => item.order == addSectionOrder);
          if(scrollTo[0]) {
            this.$nextTick(function () {
              this.scrollToSection(scrollTo[0]['id']);
            })
          }
          this.closeBuilder();
        }).catch(error => {
          if (error.response) {
            console.log(error.response);
          }
        });
      },
      deleteSection(section) {
        if( this.currentSection !== null && section.id == this.currentSection.id) {
          this.currentSection = null;
        }
        axios
        .post('/api/builder/detachSection',{id: section.id,template_id: this.data.template_id})
        .then(response => {
          this.dataSections = response.data;
        }).catch(error => {
          if (error.response) {
            console.log(error.response);
          }
        });
      },
      editSection(section) {
        this.currentSection = section;
        this.scrollToSection(section.id);
        this.isOpen = true;
      },
      insertSection(section) {
        this.insertBeforeOrder = section.order;
        this.openBuilder();
      },
      updateSections(section_id = null, sections = this.dataSections) {
        axios
        .post('/api/builder/updateSection',{
          template_id: this.data.template_id,
          sections: sections
        })
        .then(response => {
          if(section_id != null) {
            this.scrollToSection(section_id);
          }
          this.hasEvent = false;
        }).catch(error => {
          if (error.response) {
            console.log(error.response);
          }
        });
      },
      openBuilder() {
        this.currentSection = null;
        this.isOpen = true;
      },
      closeBuilder() {
        this.isOpen = false;
        this.insertBeforeOrder = null;
      },
      changeOrder(section, direction) {
        let index = this.dataSections.findIndex(item => item.id === section.id);
        if(index >= 0) {
          let copySections = this.dataSections;
          let order = section.order;
          let neighbor_index = null;
          this.currentSection = section;

          if(direction === 'up' && index > 0) {
           neighbor_index = index - 1;
          }
          if(direction === 'down' && index < (this.dataSections.length - 1)) {
            neighbor_index = index + 1;
          }

          if(neighbor_index !== null) {
            this.hasEvent = true;
            copySections[index]['order'] = copySections[neighbor_index]['order'];
            copySections[neighbor_index]['order'] = order;
            copySections.sort((a, b) =>  {
              return a.order - b.order;
            });
            this.updateSections(section.id, copySections);
          }
        }
      },
      updateAndClose() {
        this.updateSections();
        this.closeBuilder();
      },
      scrollToSection(section_id) {
        this.$nextTick(function () {
          let nodeObj = document.getElementById('section-'+section_id);
          nodeObj.scrollIntoView({
             behavior: "smooth",
             block:    "center"
          });
        })
      },
      scrollTo(event) {
        let selector = event.target.getAttribute("href");
        let nodeObj = document.querySelector(selector);
        nodeObj.scrollIntoView({
           behavior: "smooth",
           block:    "center"
        });
      }
    },
    mounted() {
      let navCompany = document.getElementById("nav-company");
      if(navCompany) {
        navCompany.addEventListener('wheel', function(event) {
          if (event.deltaMode == event.DOM_DELTA_PIXEL) {
            let modifier = 1;
          } else if (event.deltaMode == event.DOM_DELTA_LINE) {
            let modifier = parseInt(getComputedStyle(this).lineHeight);
          } else if (event.deltaMode == event.DOM_DELTA_PAGE) {
            let modifier = this.clientHeight;
          }
          if (event.deltaY != 0) {
            this.scrollLeft += modifier * event.deltaY;
            event.preventDefault();
          }
        });
      }
    },

  }
</script>

<template>

<!--  Сначада создаем ref="dropzone" и подключаем его в data(), далее инициализируем в mounted()-->
    <div class="w-25">
        <input v-model="title" type="text" class="mb-3 form-control" placeholder="title">
        <div ref="dropzone" class="mb-3 btn d-block p-5 bg-dark text-center text-light">
            Upload
        </div>

        <!--   vue-editor     -->
        <div class="mb-3">
            <vue-editor useCustomImageHandler @image-added="handleImageAdded" v-model="content" />
        </div>

        <!--   Кнопка     -->
        <input @click.prevent="store" type="submit" class="btn btn-primary" value="add">

        <div class="m-5">
<!--          Если post сущетсвует то отображаем загруженные изображения -->
<!--          Как я понял, имена id, title, images, url были добавлены в Resource/Post/PostResource и Resource/Image/ImageResource  -->
            <div v-if="post">
                <h4> {{ post.title }} </h4>
                <div v-for="image in post.images" class="mb-3">
                    <img :src="image.preview_url" class="mb-3">
                    <img :src="image.url">

                    <div class="ql-editor" v-html="post.content"></div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropzone from 'dropzone'
import { VueEditor } from "vue2-editor";
export default {
    name: "Index",

    data() {
        return {
            dropzone: null,
            title: null,
            post: null,
            content: null
        }
    },

    // Вставляем VueEditor
    components: {
        VueEditor
    },

    mounted() {
        // Подключаем к переменной dropzone класс Dropzone
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: "/api/posts",

            // Регулируем опции в Dropzone | Вкл/Выкл
            autoProcessQueue: false,
            addRemoveLinks: true,
        })

        this.getPost()
    },

    methods: {
        store() {
            // Обращаемся к методу Dropzone, что бы выцепить все изображения, которые были помещены в созданный шаблон изображений
            // console.log(this.dropzone.getAcceptedFiles());

            // Создаем класс FormData и загружаем туда фото через foreach
            // Таким образом мы можем отдавать файлы на бэкенд
            const data = new FormData()
            const files = this.dropzone.getAcceptedFiles()
                files.forEach ( file => {
                    data.append('images[]', file)

                    // После добавления в бд удаляем фото с фронта
                    this.dropzone.removeFile(file)
                })
            data.append('title', this.title)
            data.append('content', this.content)

            // Убираем написанное в title, content
            this.title = ''
            this.content = ''

            axios.post('/api/posts', data)
            .then( res => {
                this.getPost()
            })
        },

        getPost() {
            axios.get('/api/posts')
            .then ( res => {
                console.log(res);
                this.post = res.data.data
            })
        },

        // VueEditor
        handleImageAdded(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);

            axios({
                url: "/api/posts/images",
                method: "POST",
                data: formData
            })
                .then(result => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch(err => {
                    console.log(err);
                });
        }
    }
}
</script>

<style>
/*Что бы убрать ненужные классы убираем 'scoped'*/

.dz-success-mark,
.dz-error-mark {
        display: none;
    }


</style>

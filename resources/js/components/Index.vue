<template>

<!--  Сначада создаем ref="dropzone" и подключаем его в data(), далее инициализируем в mounted()-->
    <div class="w-25">
        <input v-model="title" type="text" class="mb-3 form-control" placeholder="title">
        <div ref="dropzone" class="mb-3 btn d-block p-5 bg-dark text-center text-light">
            Upload
        </div>
        <input @click.prevent="store" type="submit" class="btn btn-primary" value="add">

        <div class="m-5">

<!--          Если post сущетсвует то отображаем загруженные изображения -->
<!--          Как я понял, имена id, title, images, url были добавлены в Resource/Post/PostResource и Resource/Image/ImageResource  -->
            <div v-if="post">
                <h4> {{ post.title }} </h4>
                <div v-for="image in post.images" class="mb-3">
                    <img :src="image.preview_url" class="mb-3">
                    <img :src="image.url">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropzone from 'dropzone'
export default {
    name: "Index",

    data() {
        return {
            dropzone: null,
            title: null,
            post: null
        }
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

            // Убераем написанное в title
            this.title = ''

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
        }
    }
}
</script>

<style scoped>

</style>

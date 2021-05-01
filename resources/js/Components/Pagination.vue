<template>
  <v-pagination
		v-model="page"
    class="mb-8"
    :length="pageCount"
    :total-visible="10"
	></v-pagination>
</template>

<script>
import { getUrlParam } from '@/util'

export default {
  props: {
    quizCount: {
			type: Number,
			default: 0,
		},
		currentPage: {
			type: Number,
			default: 1,
		},
		perPage: {
			type:Number,
			required: true,
		},
    actionPath: {
      type: String,
      required: true,
    },
    attachedUrlParams: {
      type: Array,
      defalut: [],
    }
  },
  data() {
    return {
      page: this.currentPage,
    }
  },
  computed: {
    pageCount() {
			return Math.ceil(this.quizCount / this.perPage)
		}
  },
  watch: {
		page() {
			let data = {}

			let item;
      for(let param of this.attachedUrlParams) {
        item = getUrlParam(param)
			  if (item !== '') {
				  data[param] = item
			  }
      }

			data.page = this.page

			this.$inertia.get(route(this.actionPath), data);
		}
	}
}
</script>

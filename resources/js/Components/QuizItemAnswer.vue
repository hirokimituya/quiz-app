<template>
  <v-sheet class="mb-8">
    <v-row no-gutters>
      <v-col cols="12" md="2">
        <v-row no-gutters justify="space-between">
          <v-col cols="7" md="12">
            <h3 class="text-h5 mb-3">問題{{ num }}</h3>
          </v-col>
          <v-col class="text-right text-md-left">
            <span v-if="!!correctRate"> 正解率：{{ correctRate }}% </span>
          </v-col>
        </v-row>
      </v-col>

      <v-col>
        <v-card color="primary lighten-4" elevation="0">
          <v-row no-gutters class="mb-n3">
            <v-col cols="12" sm="2" class="mb-n6">
              <v-card-title>問題文</v-card-title>
            </v-col>
            <v-col>
              <v-card-text class="mt-1 text-subtitle-1">
                {{ data_item.question }}
              </v-card-text>
            </v-col>
          </v-row>

          <v-row no-gutters class="position-relative">
            <v-col cols="12" sm="2" class="mb-n6">
              <v-card-title>回答</v-card-title>
            </v-col>
            <v-col>
              <v-card-text>
                <div v-if="data_item.answerFormat == 1">
                  <v-textarea
                    v-model="data_item.answerText"
                    solo
                    dense
                    required
                    rows="1"
                    readonly
                  ></v-textarea>
                </div>

                <div
                  v-if="
                    data_item.answerFormat == 2 || data_item.answerFormat == 3
                  "
                >
                  <v-row no-gutters>
                    <v-col cols="12" class="mt-n5">
                      <div v-if="data_item.answerFormat == 2" class="mb-n3">
                        <v-radio-group
                          v-model="data_item.answerRadio"
                          required
                          readonly
                        >
                          <div
                            v-for="n in Number(data_item.selectItemsNum)"
                            :key="n"
                          >
                            <v-radio :value="n" mandatory>
                              <template #label>
                                <v-card-text class="text-subtitle-1">
                                  {{ data_item.selectItemText[num2eng(n)] }}
                                </v-card-text>
                              </template>
                            </v-radio>
                          </div>
                        </v-radio-group>
                      </div>

                      <div v-if="data_item.answerFormat == 3" class="mt-5">
                        <div
                          v-for="n in Number(data_item.selectItemsNum)"
                          :key="n"
                          class="mt-n4"
                        >
                          <v-checkbox
                            v-model="data_item.answerCheck"
                            hide-details
                            multiple
                            :value="n"
                            readonly
                          >
                            <template #label>
                              <v-card-text class="text-subtitle-1">
                                {{ data_item.selectItemText[num2eng(n)] }}
                              </v-card-text>
                            </template>
                          </v-checkbox>
                        </div>
                      </div>
                    </v-col>
                  </v-row>
                </div>
              </v-card-text>
            </v-col>

            <div v-if="data_item.pass" class="position-absolute-circle">
              <!-- マルの画像 -->
              <svg
                width="200"
                height="200"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
              >
                <circle
                  cx="100"
                  cy="100"
                  r="50"
                  stroke="red"
                  fill="rgba(0, 0, 0, 0)"
                  stroke-width="3"
                />
              </svg>
            </div>
            <div v-else class="position-absolute-cross">
              <!-- バツの画像 -->
              <svg
                xmlns:svg="http://www.w3.org/2000/svg"
                xmlns="http://www.w3.org/2000/svg"
                version="1.1"
                style="overflow:visible"
                viewBox="-48 -49 100 100"
                height="100px"
                width="100px"
              >
                <rect
                  fill-opacity="0"
                  fill="rgb(0,0,0)"
                  height="100"
                  width="100"
                  y="-49"
                  x="-48"
                />
                <svg
                  version="1.1"
                  y="-250"
                  x="-250"
                  viewBox="-250 -250 500 500"
                  height="500px"
                  width="500px"
                  style="overflow:visible"
                >
                  <g
                    transform="rotate(45,0,0)"
                    stroke-linejoin="round"
                    fill="#fff"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="3"
                      stroke-opacity="1"
                      stroke="rgb(255,0,0)"
                      fill="none"
                      d="m0-66.494624v132.9891"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="3"
                      stroke-opacity="1"
                      stroke="rgb(255,0,0)"
                      fill="none"
                      d="m-66.494624 0h132.9891"
                    />
                  </g>
                </svg>
              </svg>
            </div>
          </v-row>

          <v-row no-gutters class="mt-n5">
            <v-col cols="12" sm="2" class="mb-n6">
              <v-card-title class="font-weight-bold">正解</v-card-title>
            </v-col>
            <v-col>
              <v-card-text class="font-weight-bold text-subtitle-1">
                {{ correctDisplay }}
              </v-card-text>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-sheet>
</template>

<script>
import { num2eng } from '@/util'
import { putDesimalPointTwo } from '@/util'

export default {
  props: {
    item: {
      type: Object,
      required: true,
    },
    num: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      data_item: this.item,
    }
  },
  computed: {
    correctDisplay() {
      if (Array.isArray(this.data_item.correct)) {
        return this.data_item.correct.join(', ')
      } else {
        return this.data_item.correct
      }
    },
    correctRate() {
      if (this.data_item.correctRate != null) {
        return putDesimalPointTwo(this.data_item.correctRate)
      }
      return false
    },
  },
  methods: {
    num2eng(num) {
      return num2eng(num)
    },
  },
}
</script>

<style scoped>
.position-relative {
  position: relative;
}
.position-absolute-circle {
  position: absolute;
  top: -60px;
  left: 90px;
}
.position-absolute-cross {
  position: absolute;
  top: -10px;
  left: 140px;
}
</style>

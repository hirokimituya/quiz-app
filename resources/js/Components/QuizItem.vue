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
                {{ value.question }}
              </v-card-text>
            </v-col>
          </v-row>

          <v-row no-gutters>
            <v-col cols="12" sm="2" class="mb-n6">
              <v-card-title>回答</v-card-title>
            </v-col>
            <v-col>
              <v-card-text>
                <div v-if="value.answerFormat == 1">
                  <v-textarea
                    solo
                    dense
                    required
                    rows="1"
                    :readonly="readonly"
                    v-model="emitData.answerText"
                    @change="onChange"
                  ></v-textarea>
                </div>

                <div v-if="value.answerFormat == 2 || value.answerFormat == 3">
                  <v-row no-gutters>
                    <v-col cols="12" class="mt-n5">
                      <div v-if="value.answerFormat == 2" class="mb-n3">
                        <v-radio-group
                          required
                          v-model="emitData.answerRadio"
                          :readonly="readonly"
                          @change="onChange"
                        >
                          <div
                            v-for="n in Number(value.selectItemsNum)"
                            :key="n"
                          >
                            <v-radio :value="n" mandatory>
                              <template #label>
                                <v-card-text class="text-subtitle-1">
                                  {{ value.selectItemText[num2eng(n)] }}
                                </v-card-text>
                              </template>
                            </v-radio>
                          </div>
                        </v-radio-group>
                      </div>

                      <div v-if="value.answerFormat == 3" class="mt-5">
                        <div
                          v-for="n in Number(value.selectItemsNum)"
                          :key="n"
                          class="mt-n4"
                        >
                          <v-checkbox
                            v-model="emitData.answerCheck"
                            hide-details
                            multiple
                            :value="n"
                            :readonly="readonly"
                            @change="onChange"
                          >
                            <template #label>
                              <v-card-text class="text-subtitle-1">
                                {{ value.selectItemText[num2eng(n)] }}
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
    value: {
      type: Object,
      required: true,
    },
    num: {
      type: Number,
      required: true,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      emitData: {
        question: this.value.question || null,
        answerFormat: this.value.answerFormat || null,
        answerText: this.value.answerText || null,
        answerRadio: Number(this.value.answerRadio) || null,
        answerCheck: this.value.answerCheck
          ? this.value.answerCheck.map(n => Number(n))
          : null,
        selectItemText: this.value.selectItemText || null,
        selectItemsNum: this.value.selectItemsNum || null,
        correctRate: this.value.correctRate,
      },
    }
  },
  computed: {
    correctRate() {
      if (this.value.correctRate != 'undefined') {
        return putDesimalPointTwo(this.value.correctRate)
      }
      return false
    },
  },
  methods: {
    onChange() {
      let datas = this.emitData
      for (let key in datas) {
        if (datas[key] === null) {
          delete datas[key]
        }
      }

      this.$emit('input', datas)
    },
    num2eng(num) {
      return num2eng(num)
    },
  },
}
</script>

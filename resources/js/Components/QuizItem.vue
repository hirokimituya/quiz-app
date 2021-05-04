<template>
	<v-sheet class="mb-8">
		<v-row no-gutters>
			<v-col cols="12" md="2">
				<h3 class="text-h5">問題{{ num }}</h3>
			</v-col>

			<v-col>
				<v-card color="primary lighten-4" elevation="0">
					<v-row no-gutters class="mb-n3">
						<v-col cols="4" md="2">
							<v-card-title>問題文</v-card-title>
						</v-col>
						<v-col>
							<v-card-text class="mt-1 text-subtitle-1">
								{{ value.question }}
							</v-card-text>
						</v-col>
					</v-row>

					<v-row no-gutters>
						<v-col cols="3" md="2">
							<v-card-title>回答</v-card-title>
						</v-col>
						<v-col>
							<v-card-text>

								<div v-if="value.answerFormmat == 1">
									<v-textarea
										solo
										dense
										required
										rows="1"
										:disabled="disabled"
										v-model="emitData.answerText"
										@change="onChange"
									></v-textarea>
								</div>

								<div v-if="value.answerFormmat == 2 || value.answerFormmat == 3">
									<v-row no-gutters>
										<v-col cols="12" class="mt-n5">
											<div v-if="value.answerFormmat == 2">
												<v-radio-group 
													required 
													v-model="emitData.answerRadio"
													:disabled="disabled"
													@change="onChange"
												>
													<div v-for="n in Number(value.selectItemsNum)" :key="n">
														<v-row no-gutters align-content="center">
															<v-col cols="2" sm="1">
																<v-radio
																	class="mt-2"
																	:value="n"
																	mandatory
																></v-radio>
															</v-col>
															<v-col sm="8">
																<v-card-text class="text-subtitle-1 mt-n3 ml-n3">
																	{{ value.selectItemText[num2eng(n)] }}
																</v-card-text>
															</v-col>
														</v-row>
													</div>
												</v-radio-group>
											</div>

											<div v-if="value.answerFormmat == 3" class="mt-5">
												<div v-for="n in Number(value.selectItemsNum)" :key="n">
													<v-row no-gutters align-content="center">
														<v-col cols="2" sm="1">
															<v-checkbox
																v-model="answerCheckPerseInte"
																hide-details
																multiple
																:value='n'
																:disabled="disabled"
																class="shrink mr-4 mt-1"
															></v-checkbox>
														</v-col>
														<v-col sm="8">
															<v-card-text class="text-subtitle-1 mt-n3 ml-n3">
																{{ value.selectItemText[num2eng(n)] }}
															</v-card-text>
														</v-col>
													</v-row>
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
		disabled: {
			type: Boolean,
			default: false,
		}
	},
	data() {
		return {
			emitData: {
				answerText: this.value.answerText || null,
				answerRadio: Number(this.value.answerRadio) || null,
				answerCheck: this.value.answerCheck || null,
			},
		}
	},
	computed: {
		answerCheckPerseInte() {
			return this.emitData.answerCheck.map(str => parseInt(str, 10))
		}
	},
	methods: {
		onChange() {
			this.$emit('input', this.emitData)
		},
    num2eng(num) {
      return num2eng(num)
    },
	},
}
</script>
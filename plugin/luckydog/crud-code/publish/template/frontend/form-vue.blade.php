<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
@php
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $packageName = strtolower($codeGenerator['module'] ?? '');
    $componentName = $table['pascalCase'] ?? '';
    $camelCaseName = $table['camelCase'] ?? '';
@endphp
<script setup lang="ts">
import type { {{ $componentName }}Vo } from '~/{{ $packageName }}/api/{{ $camelCaseName }}.ts'
import { create, save } from '~/{{ $packageName }}/api/{{ $camelCaseName }}.ts'
import getFormItems from './components/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

const { formType = 'add', data = null } = defineProps<{
  formType: 'add' | 'edit'
  data?: {{ $componentName }}Vo | null
}>()

const t = useTrans().globalTrans
const maFormRef = ref<MaFormExpose>()
const formModel = ref<{{ $componentName }}Vo>({})

useForm('maFormRef').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      formModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, formModel.value))
  form.setOptions({
    labelWidth: '120px',
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    create(formModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    save(formModel.value.id as number, formModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: maFormRef,
})
</script>

<template>
  <ma-form ref="maFormRef" v-model="formModel" />
</template>

<style scoped lang="scss">

</style>

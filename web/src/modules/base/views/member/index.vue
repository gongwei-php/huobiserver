<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { page } from '~/base/api/member'
import { page as levelList } from '~/base/api/membervip'
import getSearchItems from './data/getSearchItems.tsx'
import getTableColumns from './data/getTableColumns.tsx'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

defineOptions({ name: 'member' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const selections = ref<any[]>([])
const i18n = useTrans() as TransType
const t = i18n.globalTrans
const msg = useMessage()

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '750px',
  // 保存数据
  ok: ({ formType }, okLoadingState: (state: boolean) => void) => {
    okLoadingState(true)
    if (['add', 'edit'].includes(formType)) {
      const elForm = formRef.value.maForm.getElFormRef()
      // 验证通过后
      elForm.validate().then(() => {
        switch (formType) {
          // 新增
          case 'add':
            formRef.value.add().then((res: any) => {
              res.code === ResultCode.SUCCESS ? msg.success(t('crud.createSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
          // 修改
          case 'edit':
            formRef.value.edit().then((res: any) => {
              res.code === 200 ? msg.success(t('crud.updateSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
        }
      }).catch()
    }
    okLoadingState(false)
  },
})

// 参数配置
const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  header: {
    mainTitle: () => t('baseMemberManage.mainTitle'),
    subTitle: () => t('baseMemberManage.subTitle'),
  },
  // 表格参数
  tableOptions: {
    on: {
      // 表格选择事件
      onSelectionChange: (selection: any[]) => selections.value = selection,
    },
  },
  // 搜索参数
  searchOptions: {
    fold: false,
    text: {
      searchBtn: () => t('crud.search'),
      resetBtn: () => t('crud.reset'),
      isFoldBtn: () => t('crud.searchFold'),
      notFoldBtn: () => t('crud.searchUnFold'),
    },
  },
  // 搜索表单参数
  searchFormOptions: { labelWidth: '90px' },
  // 请求配置
  requestOptions: {
    api: page,
  },
  onSearchReset: () => {
    proTableRef.value.setRequestParams({ department_id: undefined }, false)
  },
})

const levelData = ref<any[]>([{ id: 0, name: '所有等级' }]) // 初始值就给一个，防止空

provide('levelData', levelData)

// 请求等级数据
function getLevel() {
  levelList({}).then((res) => {
    let list = res.data.list as any[]
    const formatList = list.map((item: any) => ({
      id: item.level,
      name: `VIP${item.level}级`
    }))
    // 重新赋值
    levelData.value = formatList
  })
}

getLevel()
// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: getSearchItems(t, levelData),
  // 表格列
  tableColumns: getTableColumns(maDialog, formRef, t),
})

// 批量删除
// function handleDelete() {
//   const ids = selections.value.map((item: any) => item.id)
//   msg.confirm(t('crud.delMessage')).then(async () => {
//     const response = await deleteByIds(ids)
//     if (response.code === ResultCode.SUCCESS) {
//       msg.success(t('crud.delSuccess'))
//       await proTableRef.value.refresh()
//     }
//   })
// }
</script>

<template>
  <div class="mine-layout pt-3">
    <MaProTable ref="proTableRef" :options="options" :schema="schema">
      <!-- <template #actions>
        <el-button v-auth="['member:save']" type="primary" @click="() => {
          maDialog.setTitle(t('crud.add'))
          maDialog.open({ formType: 'add' })
        }">
          {{ t('crud.add') }}
        </el-button>
      </template> -->

      <!-- <template #toolbarLeft>
        <el-button v-auth="['member:delete']" type="danger" plain @click="handleDelete">
          {{ t('crud.delete') }}
        </el-button>
      </template> -->
    </MaProTable>

    <!-- <component :is="maDialog.Dialog">
      <template #default="{ formType, data }">
        <MemberForm v-if="['add', 'edit'].includes(formType)" ref="formRef" :form-type="formType" :data="data" />
      </template>
    </component> -->
  </div>
</template>

<style scoped lang="scss"></style>

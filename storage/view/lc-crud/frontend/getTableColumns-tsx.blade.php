@php
// 使用传入的 codeGenerator 数据
$table = $codeGenerator['table'] ?? [];
$packageName = strtolower($codeGenerator['module'] ?? '');
$componentName = $table['pascalCase'] ?? '';
$camelCaseName = $table['camelCase'] ?? '';
$snakeComponentName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $componentName));
@endphp


import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import type { {{$componentName}}Vo } from '~/{{$packageName}}/api/{{$camelCaseName}}.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { useMessage } from '@/hooks/useMessage.ts'
import { deleteByIds } from '~/{{$packageName}}/api/{{$camelCaseName}}.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'
import { ElTag } from 'element-plus'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {
const dictStore = useDictStore()
const msg = useMessage()

const showBtn = (auth: string | string[], row: {{$componentName}}Vo) => {
return hasAuth(auth)
}

return [
// 多选列
{ type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
// 索引序号列
{ type: 'index' },
// 普通列
@foreach($codeGenerator['formFields'] ?? [] as $field)
@if($field['is_list'] ?? false)
@php
$label = $field['label'];
if (strpos($label, ':') !== false) {
$parts = explode(':', $label, 2);
$label = trim($parts[0]);
}
@endphp
{
label: () => '{{$label}}', // t('{{$packageName . $componentName}}.{{$field['field']}}')
prop: '{{$field['field']}}',
@if($field['sortable'] ?? false)
sortable: 'custom',
@endif
@if($field['component'] == 'el-switch')
cellRender: ({ row }) => (
<ElTag type={row.{{$field['field']}}==1 ? 'success' : 'danger' }>
  {row.{{$field['field']}}_text}
</ElTag>
),
@elseif($field['component'] == 'el-select')
cellRender: ({ row }) => (
row.{{$field['field']}}_text || row.{{$field['field']}}
),
@elseif($field['component'] == 'ma-upload-image')
cellRender: ({ row }) => (
<div class="flex-center">
  <el-image class="max-w-32" src={(row.{{$field['field']}}==='' || !row.{{$field['field']}}) ? '' : row.{{$field['field']}}} alt='' />
</div>
),
@endif
},
@endif
@endforeach
// 操作列
{
type: 'operation',
label: () => t('crud.operation'),
width: '260px',
operationConfigure: {
type: 'tile',
actions: [
{
name: 'edit',
icon: 'i-heroicons:pencil',
show: ({ row }) => showBtn('{{$packageName}}:{{$snakeComponentName}}:save', row),
text: () => t('crud.edit'),
linkProps: { type: 'primary' },
onClick: ({ row }) => {
dialog.setTitle(t('crud.edit'))
dialog.open({ formType: 'edit', data: row })
},
},
{
name: 'del',
show: ({ row }) => showBtn('{{$packageName}}:{{$snakeComponentName}}:delete', row),
icon: 'i-heroicons:trash',
text: () => t('crud.delete'),
linkProps: { type: 'danger' },
onClick: ({ row }, proxy: MaProTableExpose) => {
msg.delConfirm(t('crud.delDataMessage')).then(async () => {
const response = await deleteByIds([row.id])
if (response.code === ResultCode.SUCCESS) {
msg.success(t('crud.delSuccess'))
await proxy.refresh()
}
})
},
},
],
},
},
]
}
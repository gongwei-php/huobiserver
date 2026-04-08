

import type { MaSearchItem } from '@mineadmin/search'
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'

export default function getSearchItems(t: any, levelData: any): MaSearchItem[] {
  return [
    {
      label: () => t('baseMemberManage.account'),
      prop: 'account',
      render: 'input',
    },
    {
      label: () => t('baseMemberManage.phone'),
      prop: 'phone',
      render: 'input',
    },
    {
      label: () => t('baseMemberManage.vip_level_id'),
      prop: 'vip_level_id',
      render: () => <el-tree-select />,
      renderProps: {
        data: levelData,
        multiple: false,
        filterable: true,
        clearable: true,
        props: { label: 'name' },
        checkStrictly: true,
        nodeKey: 'id',
        placeholder: t('form.pleaseInput', { msg: t('baseMemberManage.vip_level_id') }),
      },
    },
    {
      label: () => t('crud.status'),
      prop: 'status',
      render: () => MaDictSelect,
      renderProps: {
        clearable: true,
        placeholder: '',
        dictName: 'system-status',
      },
    },
  ]
}

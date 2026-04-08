

import type { MaSearchItem } from '@mineadmin/search'
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'

export default function getSearchItems(t: any): MaSearchItem[] {
  return [
    {
      label: () => t('baseMemberVipManage.level'),
      prop: 'level',
      render: 'input',
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

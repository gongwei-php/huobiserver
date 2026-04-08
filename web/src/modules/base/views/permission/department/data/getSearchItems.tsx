

import type { MaSearchItem } from '@mineadmin/search'

export default function getSearchItems(t: any): MaSearchItem[] {
  return [
    {
      label: () => t('baseDepartment.name'),
      prop: 'name',
      render: 'input',
    },
  ]
}

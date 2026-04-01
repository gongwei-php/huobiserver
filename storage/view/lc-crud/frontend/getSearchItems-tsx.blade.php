@php
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $packageName = strtolower($codeGenerator['module'] ?? '');
    $componentName = $table['pascalCase'] ?? '';
@endphp
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo <root@imoi.cn>
 * @Link   https://github.com/mineadmin
*/

import type { MaSearchItem } from '@mineadmin/search'

export default function getSearchItems(t: any): MaSearchItem[] {
  return [
@foreach($codeGenerator['formFields'] ?? [] as $field)
@if($field['is_query'] ?? false)
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
      render: () => <{{$field['component'] ? (($field['component'] == 'el-switch' || $field['component'] == 'el-select') ? 'ma-dict-select' : $field['component'] ): 'el-input'}} />,
@if(!empty($field['component_config']))
      renderProps: {
@php
      $first = true;
      foreach ($field['renderProps'] as $key => $val) {
        if (!$first) {
          echo "\n        ";
        } else {
          echo "        ";
        }
        $first = false;

        // 键名不需要引号，除非包含特殊字符
        if (preg_match('/^[a-zA-Z_$][a-zA-Z0-9_$]*$/', $key)) {
          echo $key;
        } else {
          echo "'" . $key . "'";
        }

        echo ": ";

        // 根据值类型进行处理
        if (is_array($val)) {
          // 格式化数组以符合 ESLint 要求
          if (array_is_list($val)) {
            // 索引数组
            echo "[\n";
            foreach ($val as $index => $item) {
              echo "          ";
              if (is_array($item)) {
                echo "{ ";
                $itemPairs = [];
                foreach ($item as $itemKey => $itemValue) {
                  $formattedKey = preg_match('/^[a-zA-Z_$][a-zA-Z0-9_$]*$/', $itemKey) ? $itemKey : "'$itemKey'";
                  if (is_string($itemValue)) {
                    $itemPairs[] = "$formattedKey: '$itemValue'";
                  } elseif (is_numeric($itemValue)) {
                    $itemPairs[] = "$formattedKey: $itemValue";
                  } elseif (is_bool($itemValue)) {
                    $itemPairs[] = "$formattedKey: " . ($itemValue ? 'true' : 'false');
                  } else {
                    $itemPairs[] = "$formattedKey: " . json_encode($itemValue, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                  }
                }
                echo implode(", ", $itemPairs) . " }";
              } else {
                echo json_encode($item, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
              }
              echo ",";
              echo "\n";
            }
            echo "        ]";
          } else {
            // 关联数组
            echo json_encode($val, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
          }
        } elseif (is_bool($val)) {
          echo $val ? 'true' : 'false';
        } elseif (is_null($val)) {
          echo 'null';
        } elseif (is_numeric($val)) {
          echo $val;
        } elseif (is_string($val) && strpos($val, "t('") === 0) {
          echo $val;
        } else {
          echo "'" . str_replace("'", "\\'", $val) . "'";
        }
        echo ",";
      }
      echo "\n      ";
@endphp
},
@endif
    },
@endif
@endforeach
  ]
}

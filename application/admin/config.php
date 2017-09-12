<?php
return [

	'paginate'      => [
        'type'      => 'Bootstrap',
        'var_page'  => 'page',
        'list_rows' =>20,
        'newstyle'  => true,
        'query' => input('param.'),
    ],
];
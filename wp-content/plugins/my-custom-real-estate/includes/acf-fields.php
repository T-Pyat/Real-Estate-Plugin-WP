<?php

acf_add_local_field_group([
    'key' => 'group_estate_fields',
    'title' => 'Дані про обʼєкт нерухомості',
    'fields' => [

        [
            'key' => 'field_estate_name',
            'label' => 'Назва будинку',
            'name' => 'estate_name',
            'type' => 'text',
        ],
        [
            'key' => 'field_estate_coords',
            'label' => 'Координати місцезнаходження',
            'name' => 'estate_coords',
            'type' => 'text',
        ],
        [
            'key' => 'field_floors_count',
            'label' => 'Кількість поверхів',
            'name' => 'floors_count',
            'type' => 'select',
            'choices' => array_combine(range(1, 20), range(1, 20)),
        ],
        [
            'key' => 'field_building_type',
            'label' => 'Тип будівлі',
            'name' => 'building_type',
            'type' => 'radio',
            'choices' => [
                'панель' => 'Панель',
                'цегла' => 'Цегла',
                'піноблок' => 'Піноблок',
            ],
        ],
        [
            'key' => 'field_eco_rating',
            'label' => 'Екологічність',
            'name' => 'eco_rating',
            'type' => 'select',
            'choices' => array_combine(range(1, 5), range(1, 5)),
        ],
        [
            'key' => 'field_estate_image',
            'label' => 'Зображення',
            'name' => 'estate_image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ],
        [
            'key' => 'field_rooms_repeater',
            'label' => 'Приміщення',
            'name' => 'rooms',
            'type' => 'repeater',
            'layout' => 'row',
            'sub_fields' => [
                [
                    'key' => 'field_room_area',
                    'label' => 'Площа',
                    'name' => 'area',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_room_count',
                    'label' => 'Кіл. кімнат',
                    'name' => 'room_count',
                    'type' => 'radio',
                    'choices' => array_combine(range(1, 10), range(1, 10)),
                    'layout' => 'horizontal',
                ],
                [
                    'key' => 'field_balcony',
                    'label' => 'Балкон',
                    'name' => 'balcony',
                    'type' => 'radio',
                    'choices' => ['так' => 'Так', 'ні' => 'Ні'],
                    'layout' => 'horizontal',
                ],
                [
                    'key' => 'field_wc',
                    'label' => 'Санвузол',
                    'name' => 'wc',
                    'type' => 'radio',
                    'choices' => ['так' => 'Так', 'ні' => 'Ні'],
                    'layout' => 'horizontal',
                ],
                [
                    'key' => 'field_room_image',
                    'label' => 'Зображення',
                    'name' => 'room_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ],
            ]
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'estate',
            ],
        ],
    ],
]);

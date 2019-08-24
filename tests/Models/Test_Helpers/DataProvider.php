<?php
namespace Test\Models\TestHelpers;

class DataProvider
{
    protected $data = [
        'metadata' => [
            '.tag' => 'file',
            'name' => 'Prime_Numbers.txt',
            'id' => 'id:a4ayc_80_OEAAAAAAAAAXw',
            'client_modified' => '2015-05-12T15:50:38Z',
            'server_modified' => '2015-05-12T15:50:38Z',
            'rev' => 'a1c10ce0dd78',
            'size' => 7212,
            'path_lower' => '/homework/math/prime_numbers.txt',
            'path_display' => '/Homework/math/Prime_Numbers.txt',
            'sharing_info' => [
                'read_only' => true,
                'parent_shared_folder_id' => '84528192421',
                'modified_by' => 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc'
            ],
            'is_downloadable' => true,
            'property_groups' => [
                [
                    'template_id' => 'ptid:1a5n2i6d3OYEAAAAAAAAAYa',
                    'fields' => [
                        [
                            'name' => 'Security Policy',
                            'value' => 'Confidential'
                        ]
                    ]
                ]
            ],
            'has_explicit_shared_members' => false,
            'content_hash' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'
        ],
        'copy_reference' => 'z1X6ATl6aWtzOGq0c3g5Ng',
        'expires' => '2045-05-12T15:50:38Z'
    ];

    public function getDataFile()
    {
        return $this->data;
    }

    public function getDataFolder()
    {
        return $dataFolder = [
            '.tag' => 'folder',
            'name' => 'math',
            'id' => 'id:a4ayc_80_OEAAAAAAAAAXz',
            'path_lower' => '/homework/math',
            'path_display' => '/Homework/math',
            'sharing_info' => [
                'read_only' => false,
                'parent_shared_folder_id' => '84528192421',
                'traverse_only' => false,
                'no_access' => false
            ],
            'property_groups' => [
                [
                    'template_id' => 'ptid:1a5n2i6d3OYEAAAAAAAAAYa',
                    'fields' => [
                        [
                            'name' => 'Security Policy',
                            'value' => 'Confidential'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getMetadataFile()
    {
        $data = $this->getDataFile();

        return $data['metadata'];
    }

    public function getTemporaryLink()
    {
        return [
            'metadata' => [
                'name' => 'Prime_Numbers.txt',
                'id' => 'id:a4ayc_80_OEAAAAAAAAAXw',
                'client_modified' => '2015-05-12T15:50:38Z',
                'server_modified' => '2015-05-12T15:50:38Z',
                'rev' => 'a1c10ce0dd78',
                'size' => 7212,
                'path_lower' => '/homework/math/prime_numbers.txt',
                'path_display' => '/Homework/math/Prime_Numbers.txt',
                'sharing_info' => [
                    'read_only' => true,
                    'parent_shared_folder_id' => '84528192421',
                    'modified_by' => 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc'
                ],
                'is_downloadable' => true,
                'property_groups' => [
                    [
                        'template_id' => 'ptid:1a5n2i6d3OYEAAAAAAAAAYa',
                        'fields' => [
                            [
                                'name' => 'Security Policy',
                                'value' => 'Confidential'
                            ]
                        ]
                    ]
                ],
                'has_explicit_shared_members' => false,
                'content_hash' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'
            ],
            'link' => 'https://dl.dropboxusercontent.com/apitl/1/YXNkZmFzZGcyMzQyMzI0NjU2NDU2NDU2'
        ];
    }

    public function getMetadataCollection()
    {
        $data[] = $this->getMetadataFile();
        $data[] = $this->getDataFolder();

        $sd['entries'] = $data;

        return $sd;
    }

    public function getSearchResults()
    {
        return [
            'matches' => [
                [
                    'match_type' => [
                        '.tag' => 'content'
                    ],
                    'metadata' => [
                        '.tag' => 'file',
                        'name' => 'Prime_Numbers.txt',
                        'id' => 'id:a4ayc_80_OEAAAAAAAAAXw',
                        'client_modified' => '2015-05-12T15:50:38Z',
                        'server_modified' => '2015-05-12T15:50:38Z',
                        'rev' => 'a1c10ce0dd78',
                        'size' => 7212,
                        'path_lower' => '/homework/math/prime_numbers.txt',
                        'path_display' => '/Homework/math/Prime_Numbers.txt',
                        'sharing_info' => [
                            'read_only' => true,
                            'parent_shared_folder_id' => '84528192421',
                            'modified_by' => 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc'
                        ],
                        'is_downloadable' => true,
                        'property_groups' => [
                            [
                                'template_id' => 'ptid:1a5n2i6d3OYEAAAAAAAAAYa',
                                'fields' => [
                                    [
                                        'name' => 'Security Policy',
                                        'value' => 'Confidential'
                                    ]
                                ]
                            ]
                        ],
                        'has_explicit_shared_members' => false,
                        'content_hash' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'
                    ]
                ]
            ],
            'more' => false,
            'start' => 1
        ];
    }

    public function getSearchResult()
    {
        $results = $this->getSearchResults();

        foreach ($results['matches'] as $entry) {
            if (isset($entry['metadata'])) {
                return $entry;
            }
        }
    }

    public function getMetadataDeltedFile()
    {
        $data = $this->getDataFile();

        $metadata = $data['metadata'];
        unset($metadata['.tag']);

        return $metadata;
    }

    /**
     * Adds media_info property to protected $data
     *
     * @param string $tag photo or video
     */
     public function addMediaInfoProperty($tag = 'video')
     {
         switch ($tag) {
             case 'video':

                 $this->data['metadata']['media_info'] = [
                     '.tag' => 'metadata',
                     'metadata' => [
                         '.tag' => 'video',
                         'dimensions' => [
                             'height' => 190,
                             'width' => 340
                         ],
                         'duration' => 1200
                     ]
                 ];
                 break;
             case 'photo':
                 $this->data['metadata']['media_info'] = [
                     '.tag' => 'metadata',
                     'metadata' => [
                         '.tag' => 'photo',
                         'dimensions' => [
                             'height' => 4032,
                             'width' => 3024
                         ],
                         'location' => [
                             'latitude' => 46.797977777777774,
                             'longitude' => -96.8098
                         ],
                         'time_taken' => '2018-09-26T20:42:07Z'
                     ]
                 ];
                 break;
             default:
                $this->data['metadata']['media_info'] = [
                    'metadata' => [
                        '.tag' => 'unknown'
                    ]
                ];
         }
     }

    /**
     * get media_info property
     *
     * @param  string $tag photo or video
     *
     * @return [type]      [description]
     */
    public function getMediaInfoProperty($tag = 'video')
    {
        $this->addMediaInfoProperty($tag);

        return $this->data['metadata']['media_info'];
    }

    public function getMediaInfoMetadata($tag = 'video')
    {
        $data = $this->getMediaInfoProperty($tag);

        return $data['metadata'];
    }

    public function getFileSharingInfo()
    {
        return $this->data['metadata']['sharing_info'];
    }

    public function getFolderSharingInfo()
    {
        $folder = $this->getDataFolder();

        return $folder['sharing_info'];
    }
}

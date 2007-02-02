<?php
//lucene include class
//required top level files
require_once 'core_modules/lucene/resources/Exception.php';
require_once 'core_modules/lucene/resources/Search/Exception.php';
require_once 'core_modules/lucene/resources/Search/Lucene.php';

//lucene specific files
require_once 'core_modules/lucene/resources/Search/Lucene/Document.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Exception.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Field.php';

//storage files
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/Directory.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/File.php';

//filesystem adaptors
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/Directory/Filesystem.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/File/Filesystem.php';

//analysis adaptors
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Token.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/TokenFilter.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer/Common.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer/Common/Text.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/TokenFilter/LowerCase.php';

//index adaptors
require_once 'core_modules/lucene/resources/Search/Lucene/Index/FieldInfo.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Index/SegmentInfo.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Index/SegmentWriter.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Index/Term.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Index/TermInfo.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Index/Writer.php';

//Search adaptors
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryHit.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryParser.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryToken.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryTokenizer.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Similarity.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight.php';

//Search/Query adaptors
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query/MultiTerm.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query/Phrase.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query/Term.php';

//Search/Similarity adaptor
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Similarity/Default.php';

//Search/Weight adaptors
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight/MultiTerm.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight/Phrase.php';
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight/Term.php';

?>
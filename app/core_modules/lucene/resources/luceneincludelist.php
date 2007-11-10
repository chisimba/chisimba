<?php

// Load Exception Classes
require_once('Exception.php');
require_once('10.2/Search/Exception.php');

// Load Interface Class
require_once('10.2/Search/Lucene/Interface.php');

// Load Rest of Lucene Classes
require_once('10.2/Search/Lucene.php');
require_once('10.2/Search/Lucene/Exception.php');
require_once('10.2/Search/Lucene/PriorityQueue.php');
require_once('10.2/Search/Lucene/FSMAction.php');
require_once('10.2/Search/Lucene/FSM.php');
require_once('10.2/Search/Lucene/Document.php');
require_once('10.2/Search/Lucene/Field.php');
require_once('10.2/Search/Lucene/Proxy.php');
require_once('10.2/Search/Lucene/Document/Html.php');
require_once('10.2/Search/Lucene/Analysis/TokenFilter.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer.php');
require_once('10.2/Search/Lucene/Analysis/Token.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common/Utf8.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common/TextNum.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common/Utf8Num.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common/Text.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php');
require_once('10.2/Search/Lucene/Analysis/Analyzer/Common/TextNum/CaseInsensitive.php');
require_once('10.2/Search/Lucene/Analysis/TokenFilter/StopWords.php');
require_once('10.2/Search/Lucene/Analysis/TokenFilter/LowerCase.php');
require_once('10.2/Search/Lucene/Analysis/TokenFilter/ShortWords.php');
require_once('10.2/Search/Lucene/Index/SegmentInfoPriorityQueue.php');
require_once('10.2/Search/Lucene/Index/Writer.php');
require_once('10.2/Search/Lucene/Index/FieldInfo.php');
require_once('10.2/Search/Lucene/Index/Term.php');
require_once('10.2/Search/Lucene/Index/TermInfo.php');
require_once('10.2/Search/Lucene/Index/SegmentInfo.php');
require_once('10.2/Search/Lucene/Index/SegmentWriter.php');
require_once('10.2/Search/Lucene/Index/DictionaryLoader.php');
require_once('10.2/Search/Lucene/Index/SegmentMerger.php');
require_once('10.2/Search/Lucene/Index/SegmentWriter/DocumentWriter.php');
require_once('10.2/Search/Lucene/Index/SegmentWriter/StreamWriter.php');
require_once('10.2/Search/Lucene/Search/QueryEntry.php');
require_once('10.2/Search/Lucene/Search/Similarity.php');
require_once('10.2/Search/Lucene/Search/QueryLexer.php');
require_once('10.2/Search/Lucene/Search/Query.php');
require_once('10.2/Search/Lucene/Search/QueryParserException.php');
require_once('10.2/Search/Lucene/Search/Weight.php');
require_once('10.2/Search/Lucene/Search/BooleanExpressionRecognizer.php');
require_once('10.2/Search/Lucene/Search/QueryToken.php');
require_once('10.2/Search/Lucene/Search/QueryHit.php');
require_once('10.2/Search/Lucene/Search/QueryParserContext.php');
require_once('10.2/Search/Lucene/Search/QueryParser.php');
require_once('10.2/Search/Lucene/Search/Weight/Empty.php');
require_once('10.2/Search/Lucene/Search/Weight/Term.php');
require_once('10.2/Search/Lucene/Search/Weight/Boolean.php');
require_once('10.2/Search/Lucene/Search/Weight/MultiTerm.php');
require_once('10.2/Search/Lucene/Search/Weight/Phrase.php');
require_once('10.2/Search/Lucene/Search/QueryEntry/Term.php');
require_once('10.2/Search/Lucene/Search/QueryEntry/Phrase.php');
require_once('10.2/Search/Lucene/Search/QueryEntry/Subquery.php');
require_once('10.2/Search/Lucene/Search/Query/Empty.php');
require_once('10.2/Search/Lucene/Search/Query/Insignificant.php');
require_once('10.2/Search/Lucene/Search/Query/Term.php');
require_once('10.2/Search/Lucene/Search/Query/Boolean.php');
require_once('10.2/Search/Lucene/Search/Query/MultiTerm.php');
require_once('10.2/Search/Lucene/Search/Query/Phrase.php');
require_once('10.2/Search/Lucene/Search/Similarity/Default.php');
require_once('10.2/Search/Lucene/Storage/File.php');
require_once('10.2/Search/Lucene/Storage/Directory.php');
require_once('10.2/Search/Lucene/Storage/File/Filesystem.php');
require_once('10.2/Search/Lucene/Storage/File/Memory.php');
require_once('10.2/Search/Lucene/Storage/Directory/Filesystem.php');

?>
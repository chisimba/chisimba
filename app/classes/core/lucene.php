<?php

/**
 * Lucene search subsystem include file
 * 
 * Apache Lucene is a high-performance, full-featured text search engine library (PHP5 port of the Java library from Apache project)
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       http://lucene.apache.org/java/docs/
 */

/**
 * exception handler
 */
require_once 'core_modules/lucene/resources/Exception.php';

/**
 * search exception object
 */
require_once 'core_modules/lucene/resources/Search/Exception.php';

/**
 * Search object
 */
require_once 'core_modules/lucene/resources/Search/Lucene.php';


/**
 * Document Object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Document.php';

/**
 * Doc exception object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Exception.php';

/**
 * Search Field object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Field.php';


/**
 * Directory storage object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/Directory.php';

/**
 * File Storage object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/File.php';


/**
 * Filesystem top level object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/Directory/Filesystem.php';

/**
 * File filesystem 
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Storage/File/Filesystem.php';


/**
 * Analysis object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer.php';

/**
 * Tokenizer
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Token.php';

/**
 * Token Filter
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/TokenFilter.php';

/**
 * Common analysis
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer/Common.php';

/**
 * Text analyser
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer/Common/Text.php';

/**
 * Case insensitive analysis
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php';

/**
 * Lower case analysis
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Analysis/TokenFilter/LowerCase.php';


/**
 * Indexer
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Index/FieldInfo.php';

/**
 * Segment object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Index/SegmentInfo.php';

/**
 * Segment writer
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Index/SegmentWriter.php';

/**
 * Term object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Index/Term.php';

/**
 * Term info
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Index/TermInfo.php';

/**
 * Writer object
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Index/Writer.php';


/**
 * Search adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query.php';

/**
 * Query hits
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryHit.php';

/**
 * Query parser
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryParser.php';

/**
 * Tokenizer (searches)
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryToken.php';

/**
 * Query Tokenizer
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/QueryTokenizer.php';

/**
 * Similarity parser
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Similarity.php';

/**
 * Search weighter
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight.php';

//Search/Query adaptors

/**
 * Multiterm search adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query/MultiTerm.php';

/**
 * Phrase search adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query/Phrase.php';

/**
 * Term search adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Query/Term.php';

//Search/Similarity adaptor

/**
 * Default weighter
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Similarity/Default.php';

//Search/Weight adaptors

/**
 * Multiterm Search/Weight Adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight/MultiTerm.php';

/**
 * Phrase Search/Weight Adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight/Phrase.php';

/**
 * Term Search/Weight Adaptor
 */
require_once 'core_modules/lucene/resources/Search/Lucene/Search/Weight/Term.php';

?>
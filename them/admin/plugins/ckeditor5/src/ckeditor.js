/**
 * @license Copyright (c) 2014-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */
import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor.js';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment.js';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat.js';
import AutoImage from '@ckeditor/ckeditor5-image/src/autoimage.js';
import AutoLink from '@ckeditor/ckeditor5-link/src/autolink.js';
import Autosave from '@ckeditor/ckeditor5-autosave/src/autosave.js';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote.js';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold.js';
import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder.js';
import CKFinderUploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter.js';
import CloudServices from '@ckeditor/ckeditor5-cloud-services/src/cloudservices.js';
import Code from '@ckeditor/ckeditor5-basic-styles/src/code.js';
import CodeBlock from '@ckeditor/ckeditor5-code-block/src/codeblock.js';
import Comments from '@ckeditor/ckeditor5-comments/src/comments.js';
import DataFilter from '@ckeditor/ckeditor5-html-support/src/datafilter.js';
import DataSchema from '@ckeditor/ckeditor5-html-support/src/dataschema.js';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials.js';
import ExportPdf from '@ckeditor/ckeditor5-export-pdf/src/exportpdf.js';
import ExportWord from '@ckeditor/ckeditor5-export-word/src/exportword.js';
import FindAndReplace from '@ckeditor/ckeditor5-find-and-replace/src/findandreplace.js';
import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/fontbackgroundcolor.js';
import FontColor from '@ckeditor/ckeditor5-font/src/fontcolor.js';
import FontFamily from '@ckeditor/ckeditor5-font/src/fontfamily.js';
import FontSize from '@ckeditor/ckeditor5-font/src/fontsize.js';
import GeneralHtmlSupport from '@ckeditor/ckeditor5-html-support/src/generalhtmlsupport.js';
import Heading from '@ckeditor/ckeditor5-heading/src/heading.js';
import Highlight from '@ckeditor/ckeditor5-highlight/src/highlight.js';
import HorizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline.js';
import HtmlEmbed from '@ckeditor/ckeditor5-html-embed/src/htmlembed.js';
import Image from '@ckeditor/ckeditor5-image/src/image.js';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption.js';
import ImageInsert from '@ckeditor/ckeditor5-image/src/imageinsert.js';
import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize.js';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle.js';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar.js';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload.js';
import Indent from '@ckeditor/ckeditor5-indent/src/indent.js';
import IndentBlock from '@ckeditor/ckeditor5-indent/src/indentblock.js';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic.js';
import Link from '@ckeditor/ckeditor5-link/src/link.js';
import LinkImage from '@ckeditor/ckeditor5-link/src/linkimage.js';
import List from '@ckeditor/ckeditor5-list/src/list.js';
import ListProperties from '@ckeditor/ckeditor5-list/src/listproperties.js';
import Markdown from '@ckeditor/ckeditor5-markdown-gfm/src/markdown.js';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed.js';
import PageBreak from '@ckeditor/ckeditor5-page-break/src/pagebreak.js';
import Pagination from '@ckeditor/ckeditor5-pagination/src/pagination.js';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph.js';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice.js';
import RemoveFormat from '@ckeditor/ckeditor5-remove-format/src/removeformat.js';
import RevisionHistory from '@ckeditor/ckeditor5-revision-history/src/revisionhistory.js';
import SpecialCharacters from '@ckeditor/ckeditor5-special-characters/src/specialcharacters.js';
import SpecialCharactersArrows from '@ckeditor/ckeditor5-special-characters/src/specialcharactersarrows.js';
import SpecialCharactersCurrency from '@ckeditor/ckeditor5-special-characters/src/specialcharacterscurrency.js';
import SpecialCharactersEssentials from '@ckeditor/ckeditor5-special-characters/src/specialcharactersessentials.js';
import SpecialCharactersLatin from '@ckeditor/ckeditor5-special-characters/src/specialcharacterslatin.js';
import SpecialCharactersMathematical from '@ckeditor/ckeditor5-special-characters/src/specialcharactersmathematical.js';
import SpecialCharactersText from '@ckeditor/ckeditor5-special-characters/src/specialcharacterstext.js';
import Table from '@ckeditor/ckeditor5-table/src/table.js';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar.js';
import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation.js';
import TodoList from '@ckeditor/ckeditor5-list/src/todolist';
import TrackChanges from '@ckeditor/ckeditor5-track-changes/src/trackchanges.js';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline.js';
import WordCount from '@ckeditor/ckeditor5-word-count/src/wordcount.js';
import EditorWatchdog from '@ckeditor/ckeditor5-watchdog/src/editorwatchdog.js';

class RevisionHistoryAdapter {
	static get requires() {
		return [ 'RevisionHistory' ];
	}

	constructor( editor ) {
		this.editor = editor;
	}

	init() {
		const editor = this.editor;
		const revisionHistory = editor.plugins.get( 'RevisionHistory' );
		const revisions = [];

		revisionHistory.adapter = {
			getRevision: async ( { revisionId } ) => {
				return revisions.find( data => data.id == revisionId );
			},
			updateRevisions: async revisionsData => {
				revisions.splice( 0, revisions.length, ...revisionsData );
			}
		};
	}
}

class Editor extends ClassicEditor {}

// Plugins to include in the build.
Editor.builtinPlugins = [
	Alignment,
	Autoformat,
	AutoImage,
	AutoLink,
	Autosave,
	BlockQuote,
	Bold,
	CKFinder,
	CKFinderUploadAdapter,
	CloudServices,
	Code,
	CodeBlock,
	Comments,
	DataFilter,
	DataSchema,
	Essentials,
	ExportPdf,
	ExportWord,
	FindAndReplace,
	FontBackgroundColor,
	FontColor,
	FontFamily,
	FontSize,
	GeneralHtmlSupport,
	Heading,
	Highlight,
	HorizontalLine,
	HtmlEmbed,
	Image,
	ImageCaption,
	ImageInsert,
	ImageResize,
	ImageStyle,
	ImageToolbar,
	ImageUpload,
	Indent,
	IndentBlock,
	Italic,
	Link,
	LinkImage,
	List,
	ListProperties,
	Markdown,
	MediaEmbed,
	PageBreak,
	Pagination,
	Paragraph,
	PasteFromOffice,
	RemoveFormat,
	RevisionHistory,
	SpecialCharacters,
	SpecialCharactersArrows,
	SpecialCharactersCurrency,
	SpecialCharactersEssentials,
	SpecialCharactersLatin,
	SpecialCharactersMathematical,
	SpecialCharactersText,
	Table,
	TableToolbar,
	TextTransformation,
	TodoList,
	TrackChanges,
	Underline,
	WordCount,
	RevisionHistoryAdapter
];

// Editor configuration.
Editor.defaultConfig = {
	toolbar: {
		items: [
			'|',
			'heading',
			'bold',
			'italic',
			'link',
			'bulletedList',
			'numberedList',
			'|',
			'outdent',
			'indent',
			'|',
			'imageUpload',
			'blockQuote',
			'insertTable',
			'mediaEmbed',
			'undo',
			'redo',
			'trackChanges',
			'specialCharacters',
			'removeFormat',
			'revisionHistory',
			'underline',
			'alignment',
			'CKFinder',
			'comment',
			'codeBlock',
			'code',
			'findAndReplace',
			'exportWord',
			'exportPdf',
			'fontSize',
			'fontFamily',
			'fontColor',
			'fontBackgroundColor',
			'highlight',
			'htmlEmbed',
			'imageInsert',
			'previousPage',
			'nextPage',
			'pageNavigation',
			'todoList',
			'horizontalLine'
		]
	},
	language: 'fa',
	image: {
		toolbar: [
			'comment',
			'imageTextAlternative',
			'toggleImageCaption',
			'imageStyle:inline',
			'imageStyle:block',
			'imageStyle:side',
			'linkImage'
		]
	},
	table: {
		contentToolbar: [
			'tableColumn',
			'tableRow',
			'mergeTableCells'
		],
		tableToolbar: [
			'comment'
		]
	},
	pagination: {
		pageWidth: '21cm',
		pageHeight: '29.7cm',
		pageMargins: {
			top: '20mm',
			bottom: '20mm',
			left: '12mm',
			right: '12mm'
		}
	},
	comments: {
		editorConfig: {
			extraPlugins: [
				Bold,
				Italic,
				List,
				Autoformat
			]
		}
	}
};

export default { Editor, EditorWatchdog };

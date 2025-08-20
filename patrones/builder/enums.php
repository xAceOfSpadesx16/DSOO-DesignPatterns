<?php
namespace Builder\Enums;

enum TagName: string {
    case TABLE = "table";
    case TR = "tr";
    case TD = "td";
    case TH = "th";
    case THEAD = "thead";
    case TBODY = "tbody";
    case TFOOT = "tfoot";
    case STRING = "string";
    case DEFAULT = "div";

    case P = "p";
    case H1 = "h1";
    case H2 = "h2";
    case H3 = "h3";
    case H4 = "h4";
    case H5 = "h5";
    case H6 = "h6";

}
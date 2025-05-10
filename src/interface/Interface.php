<?php

class CSS {
    // Layout
    public const display = 'display';
    public const position = 'position';
    public const top = 'top';
    public const left = 'left';
    public const right = 'right';
    public const bottom = 'bottom';
    public const zIndex = 'z-index';
    public const float = 'float';
    public const clear = 'clear';

    // Box Model
    public const width = 'width';
    public const height = 'height';
    public const maxWidth = 'max-width';
    public const maxHeight = 'max-height';
    public const minWidth = 'min-width';
    public const minHeight = 'min-height';

    public const margin = 'margin';
    public const marginTop = 'margin-top';
    public const marginRight = 'margin-right';
    public const marginBottom = 'margin-bottom';
    public const marginLeft = 'margin-left';

    public const padding = 'padding';
    public const paddingTop = 'padding-top';
    public const paddingRight = 'padding-right';
    public const paddingBottom = 'padding-bottom';
    public const paddingLeft = 'padding-left';

    public const border = 'border';
    public const borderWidth = 'border-width';
    public const borderStyle = 'border-style';
    public const borderColor = 'border-color';
    public const borderRadius = 'border-radius';

    // Typography
    public const color = 'color';
    public const fontSize = 'font-size';
    public const fontWeight = 'font-weight';
    public const fontFamily = 'font-family';
    public const textAlign = 'text-align';
    public const lineHeight = 'line-height';
    public const textDecoration = 'text-decoration';
    public const letterSpacing = 'letter-spacing';
    public const wordBreak = 'word-break';
    public const whiteSpace = 'white-space';
    public const textTransform = 'text-transform';
    public const verticalAlign = 'vertical-align';

    // Background
    public const background = 'background';
    public const backgroundColor = 'background-color';
    public const backgroundImage = 'background-image';
    public const backgroundPosition = 'background-position';
    public const backgroundSize = 'background-size';
    public const backgroundRepeat = 'background-repeat';

    // Flexbox/Grid
    public const flex = 'flex';
    public const flexDirection = 'flex-direction';
    public const flexWrap = 'flex-wrap';
    public const justifyContent = 'justify-content';
    public const alignItems = 'align-items';
    public const alignContent = 'align-content';
    public const gap = 'gap';
    public const gridTemplateColumns = 'grid-template-columns';
    public const gridTemplateRows = 'grid-template-rows';

    // Effects
    public const boxShadow = 'box-shadow';
    public const opacity = 'opacity';
    public const transition = 'transition';
    public const transform = 'transform';
    public const filter = 'filter';
}

class Attrs {
    // Core Attributes
    public const classAttr = 'class';
    public const id = 'id';
    public const style = 'style';
    public const title = 'title';

    // Links and Media
    public const href = 'href';
    public const src = 'src';
    public const alt = 'alt';
    public const width = 'width';
    public const height = 'height';

    // Form Elements
    public const name = 'name';
    public const value = 'value';
    public const type = 'type';
    public const placeholder = 'placeholder';
    public const required = 'required';
    public const disabled = 'disabled';
    public const readonly = 'readonly';
    public const checked = 'checked';
    public const selected = 'selected';
    public const multiple = 'multiple';
    public const action = 'action';
    public const method = 'method';
    public const enctype = 'enctype';
    public const for = 'for';
    public const autocomplete = 'autocomplete';
    public const accept = 'accept';
    public const maxlength = 'maxlength';
    public const min = 'min';
    public const max = 'max';
    public const step = 'step';
    public const pattern = 'pattern';

    // Button
    public const form = 'form';
    public const formaction = 'formaction';

    // Accessibility
    public const ariaLabel = 'aria-label';
    public const role = 'role';
}

class HTMLEvents
{
    // Mouse Events
    public const onClick = 'onclick';
    public const onDblClick = 'ondblclick';
    public const onMouseDown = 'onmousedown';
    public const onMouseUp = 'onmouseup';
    public const onMouseMove = 'onmousemove';
    public const onMouseOver = 'onmouseover';
    public const onMouseOut = 'onmouseout';
    public const onContextMenu = 'oncontextmenu';

    // Keyboard Events
    public const onKeyDown = 'onkeydown';
    public const onKeyPress = 'onkeypress';
    public const onKeyUp = 'onkeyup';

    // Form Events
    public const onChange = 'onchange';
    public const onSubmit = 'onsubmit';
    public const onReset = 'onreset';
    public const onInput = 'oninput';
    public const onFocus = 'onfocus';
    public const onBlur = 'onblur';
    public const onInvalid = 'oninvalid';

    // Drag and Drop Events
    public const onDrag = 'ondrag';
    public const onDrop = 'ondrop';
    public const onDragOver = 'ondragover';
    public const onDragStart = 'ondragstart';
    public const onDragEnd = 'ondragend';
    public const onDragEnter = 'ondragenter';
    public const onDragLeave = 'ondragleave';

    // Clipboard Events
    public const onCopy = 'oncopy';
    public const onCut = 'oncut';
    public const onPaste = 'onpaste';

    // Media Events
    public const onLoad = 'onload';
    public const onError = 'onerror';
    public const onAbort = 'onabort';
    public const onPlay = 'onplay';
    public const onPause = 'onpause';
    public const onEnded = 'onended';
    public const onTimeUpdate = 'ontimeupdate';
}

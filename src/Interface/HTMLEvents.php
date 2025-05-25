<?php
namespace Avetify\Interface;

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

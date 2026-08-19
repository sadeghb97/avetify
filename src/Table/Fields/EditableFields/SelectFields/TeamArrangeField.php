<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Fields\JSDatalist;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

class TeamArrangeField extends EditableField {
    public int $rows = 2;
    public int $columns = 20;

    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function setRows(int $rows): static {
        $this->rows = $rows;
        return $this;
    }

    public function setColumns(int $columns): static {
        $this->columns = $columns;
        return $this;
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        NiceDiv::justOpen($webModifier);

        $value = $this->getValue($item);
        $elementId = $this->getElementIdentifier($item);

        $recordsVar = $this->datalist->getRecordsListJSVarName();
        $mapIdVar = $this->datalist->getRecordsIdsMapJSVarName();

        ?>
        <script>
            if (typeof window.safeGetJSVar !== 'function') {
                window.safeGetJSVar = function(varName) {
                    if (!varName) return null;
                    if (typeof window[varName] !== 'undefined') return window[varName];
                    try {
                        return eval(varName);
                    } catch (e) {
                        return null;
                    }
                };
            }

            if (typeof window.toggleTeamArrangeTextarea !== 'function') {
                window.toggleTeamArrangeTextarea = function(btn) {
                    const container = btn.closest('.team-arrange-container');
                    if (!container) return;
                    const textarea = container.querySelector('textarea');
                    if (!textarea) return;
                    if (textarea.style.display === 'none') {
                        textarea.style.display = 'block';
                        textarea.focus();
                    } else {
                        textarea.style.display = 'none';
                    }
                };
            }

            if (typeof window.updateTeamArrangeField !== 'function') {
                window.updateTeamArrangeField = function(target, recordsList, recordsIdsMap) {
                    let textarea = null;
                    if (typeof target === 'string') {
                        textarea = document.getElementById(target);
                    } else if (target && target.nodeType) {
                        textarea = target;
                    }
                    if (!textarea) return;

                    const container = textarea.closest('.team-arrange-container') || textarea.parentElement;
                    if (!container) return;

                    const errorEl = container.querySelector('.team-arrange-error');
                    const schematic = container.querySelector('.team-arrange-schematic');
                    if (!errorEl || !schematic) return;

                    if (typeof recordsList === 'string') recordsList = window.safeGetJSVar(recordsList);
                    if (typeof recordsIdsMap === 'string') recordsIdsMap = window.safeGetJSVar(recordsIdsMap);

                    const val = textarea.value.trim();

                    if (!val) {
                        errorEl.style.display = "none";
                        errorEl.textContent = "";
                        schematic.innerHTML = "";
                        return;
                    }

                    const parts = val.split('##').map(p => p.trim()).filter(p => p.length > 0);
                    if (parts.length === 0) {
                        errorEl.style.display = "none";
                        errorEl.textContent = "";
                        schematic.innerHTML = "";
                        return;
                    }

                    let isValid = true;
                    const parsedTeams = [];

                    for (let i = 0; i < parts.length; i++) {
                        const part = parts[i];
                        const colonIndex = part.indexOf(':');
                        if (colonIndex === -1) {
                            isValid = false;
                            break;
                        }
                        const color = part.substring(0, colonIndex).trim();
                        const idsString = part.substring(colonIndex + 1).trim();
                        if (!color) {
                            isValid = false;
                            break;
                        }
                        const ids = idsString ? idsString.split(',').map(id => id.trim()).filter(id => id.length > 0) : [];
                        parsedTeams.push({ color, ids });
                    }

                    if (!isValid) {
                        errorEl.style.display = "block";
                        errorEl.style.color = "#dc3545";
                        errorEl.style.fontSize = "11px";
                        errorEl.style.fontWeight = "bold";
                        errorEl.style.marginTop = "3px";
                        errorEl.textContent = "Invalid format";
                        schematic.innerHTML = "";
                        return;
                    }

                    errorEl.style.display = "none";
                    errorEl.textContent = "";
                    schematic.innerHTML = "";

                    parsedTeams.forEach(team => {
                        const teamBox = document.createElement("div");
                        teamBox.className = "team-arrange-box";
                        teamBox.style.border = "1px solid " + team.color;
                        teamBox.style.borderRadius = "6px";
                        teamBox.style.padding = "4px 8px";
                        teamBox.style.backgroundColor = "rgba(0,0,0,0.015)";
                        teamBox.style.width = "100%";
                        teamBox.style.boxSizing = "border-box";
                        teamBox.style.display = "flex";
                        teamBox.style.flexDirection = "row";
                        teamBox.style.alignItems = "center";
                        teamBox.style.gap = "8px";

                        const header = document.createElement("div");
                        header.style.fontWeight = "bold";
                        header.style.fontSize = "11px";
                        header.style.color = team.color;
                        header.style.display = "flex";
                        header.style.alignItems = "center";
                        header.style.gap = "4px";
                        header.style.minWidth = "60px";

                        const dot = document.createElement("span");
                        dot.style.display = "inline-block";
                        dot.style.width = "6px";
                        dot.style.height = "6px";
                        dot.style.borderRadius = "50%";
                        dot.style.backgroundColor = team.color;

                        const titleSpan = document.createElement("span");
                        titleSpan.textContent = team.color;

                        header.appendChild(dot);
                        header.appendChild(titleSpan);
                        teamBox.appendChild(header);

                        const membersDiv = document.createElement("div");
                        membersDiv.style.display = "flex";
                        membersDiv.style.flexWrap = "wrap";
                        membersDiv.style.gap = "4px";
                        membersDiv.style.alignItems = "center";

                        team.ids.forEach(id => {
                            const lowerId = String(id).toLowerCase();
                            const recIndex = (recordsIdsMap && typeof recordsIdsMap[lowerId] !== 'undefined') ? recordsIdsMap[lowerId] : undefined;
                            if (recIndex !== undefined && recordsList && recordsList[recIndex]) {
                                const rec = recordsList[recIndex];
                                const avatarUrl = rec['main_jsdl_avatar'];
                                const name = rec['main_jsdl_name'] || id;

                                if (avatarUrl) {
                                    const img = document.createElement("img");
                                    img.src = avatarUrl;
                                    img.alt = name;
                                    img.title = name + " (" + id + ")";
                                    img.style.width = "40px";
                                    img.style.height = "auto";
                                    img.style.borderRadius = "4px";
                                    img.style.objectFit = "cover";
                                    img.style.border = "1px solid #eee";
                                    membersDiv.appendChild(img);
                                } else {
                                    const textCard = document.createElement("div");
                                    textCard.title = name + " (" + id + ")";
                                    textCard.textContent = name;
                                    textCard.style.width = "40px";
                                    textCard.style.padding = "2px 4px";
                                    textCard.style.fontSize = "9px";
                                    textCard.style.textAlign = "center";
                                    textCard.style.backgroundColor = "#f5f5f5";
                                    textCard.style.border = "1px solid #ddd";
                                    textCard.style.borderRadius = "4px";
                                    textCard.style.wordBreak = "break-word";
                                    membersDiv.appendChild(textCard);
                                }
                            } else {
                                const fallbackCard = document.createElement("div");
                                fallbackCard.title = id;
                                fallbackCard.textContent = id;
                                fallbackCard.style.width = "40px";
                                fallbackCard.style.padding = "2px 4px";
                                fallbackCard.style.fontSize = "9px";
                                fallbackCard.style.textAlign = "center";
                                fallbackCard.style.backgroundColor = "#fff";
                                fallbackCard.style.border = "1px dashed " + team.color;
                                fallbackCard.style.borderRadius = "4px";
                                fallbackCard.style.wordBreak = "break-word";
                                membersDiv.appendChild(fallbackCard);
                            }
                        });

                        teamBox.appendChild(membersDiv);
                        schematic.appendChild(teamBox);
                    });
                };
            }
        </script>
        <?php

        $updateJs = "window.updateTeamArrangeField(this,'" . addslashes($recordsVar) . "','" . addslashes($mapIdVar) . "');";

        echo '<div class="team-arrange-container" style="display: inline-block; width: fit-content; max-width: 100%;">';

        echo '<div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">';
        if ($this->title) {
            echo '<label for="' . htmlspecialchars($elementId) . '" style="font-weight: bold; font-size: 11pt; margin: 0;">' . htmlspecialchars($this->title) . '</label>';
        }
        echo '<button type="button" onclick="window.toggleTeamArrangeTextarea(this)" style="background: #f8f9fa; border: 1px solid #ced4da; border-radius: 3px; padding: 1px 5px; font-size: 10px; cursor: pointer; color: #495057; line-height: 1.2;" title="Toggle editor">✏️ Edit</button>';
        echo '</div>';

        echo '<textarea ';
        HTMLInterface::addAttribute("rows", (string)$this->rows);
        HTMLInterface::addAttribute("cols", (string)$this->columns);
        HTMLInterface::addAttribute("placeholder", $this->title);
        HTMLInterface::addAttribute("oninput", $updateJs);
        HTMLInterface::addAttribute("onkeyup", $updateJs);
        HTMLInterface::addAttribute("onchange", $updateJs);
        if ($this->isReadonly) {
            HTMLInterface::addAttribute("readonly", "readonly");
        }
        $this->appendMainAttributes($item);
        HTMLInterface::applyModifiers($webModifier);

        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::addStyle("box-sizing", "border-box");
        Styler::addStyle("resize", "vertical");
        Styler::addStyle("font-family", "inherit");
        Styler::addStyle("padding", "6px");
        Styler::addStyle("border", "1px solid #ccc");
        Styler::addStyle("border-radius", "4px");
        Styler::addStyle("display", "none");
        Styler::addStyle("max-width", "100%");
        Styler::addStyle("margin-bottom", "4px");
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();

        Styler::classStartAttribute();
        if ($this->submitter) Styler::addClass("submitter");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();

        echo '>' . htmlspecialchars((string)$value) . '</textarea>';

        echo '<div id="' . htmlspecialchars($elementId) . '_error" class="team-arrange-error" style="display: none; color: #dc3545; font-size: 11px; margin-top: 3px; font-weight: bold;"></div>';
        echo '<div id="' . htmlspecialchars($elementId) . '_schematic" class="team-arrange-schematic" style="display: flex; flex-direction: column; gap: 6px; margin-top: 4px; width: 100%; box-sizing: border-box;"></div>';

        echo '</div>';

        HTMLInterface::closeDiv();

        ?>
        <script>
            (function() {
                const fieldId = <?php echo json_encode($elementId); ?>;
                const recordsVarName = <?php echo json_encode($recordsVar); ?>;
                const mapIdVarName = <?php echo json_encode($mapIdVar); ?>;

                const runInit = function() {
                    if (typeof window.updateTeamArrangeField === 'function') {
                        window.updateTeamArrangeField(fieldId, recordsVarName, mapIdVarName);
                    }
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', runInit);
                } else {
                    runInit();
                }
            })();
        </script>
        <?php
    }

    public function preLoad() {}
}

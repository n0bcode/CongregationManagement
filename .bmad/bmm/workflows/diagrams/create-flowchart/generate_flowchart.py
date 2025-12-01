import json
import uuid
import time

# Theme Colors (Professional Blue)
THEME = {
    "primary_fill": "#e3f2fd",
    "accent": "#1976d2",
    "decision_fill": "#fff3e0",
    "decision_border": "#f57c00",
    "text": "#1e1e1e",
    "background": "#ffffff"
}

ELEMENTS = []

def get_id():
    return str(uuid.uuid4())

def create_text(text, x, y, container_id, group_id):
    font_size = 20
    # Approx width calc: (text.length * fontSize * 0.6) + 20
    width = (len(text) * font_size * 0.6) + 20
    # Round to nearest 10
    width = round(width / 10) * 10
    
    # Height approx
    height = 25 

    return {
        "id": get_id(),
        "type": "text",
        "x": x + 10, # Padding
        "y": y + (80 - height) / 2, # Vertically centered in standard 80px height
        "width": width,
        "height": height,
        "angle": 0,
        "strokeColor": THEME["text"],
        "backgroundColor": "transparent",
        "fillStyle": "hachure",
        "strokeWidth": 1,
        "strokeStyle": "solid",
        "roughness": 1,
        "opacity": 100,
        "groupIds": [group_id],
        "roundness": None,
        "seed": 1,
        "version": 1,
        "versionNonce": 0,
        "isDeleted": False,
        "boundElements": None,
        "updated": int(time.time() * 1000),
        "link": None,
        "locked": False,
        "text": text,
        "fontSize": font_size,
        "fontFamily": 1,
        "textAlign": "center",
        "verticalAlign": "middle",
        "containerId": container_id,
        "originalText": text
    }

def create_shape(type, x, y, width, height, label, is_decision=False):
    shape_id = get_id()
    group_id = get_id()
    
    bg_color = THEME["decision_fill"] if is_decision else THEME["primary_fill"]
    stroke_color = THEME["decision_border"] if is_decision else THEME["accent"]
    
    text_element = create_text(label, x, y, shape_id, group_id)
    
    # Center text in shape
    text_element["x"] = x + (width - text_element["width"]) / 2
    text_element["y"] = y + (height - text_element["height"]) / 2
    
    shape = {
        "id": shape_id,
        "type": type,
        "x": x,
        "y": y,
        "width": width,
        "height": height,
        "angle": 0,
        "strokeColor": stroke_color,
        "backgroundColor": bg_color,
        "fillStyle": "solid",
        "strokeWidth": 2,
        "strokeStyle": "solid",
        "roughness": 0,
        "opacity": 100,
        "groupIds": [group_id],
        "roundness": {"type": 3} if type == "rectangle" else None,
        "seed": 1,
        "version": 1,
        "versionNonce": 0,
        "isDeleted": False,
        "boundElements": [{"type": "text", "id": text_element["id"]}],
        "updated": int(time.time() * 1000),
        "link": None,
        "locked": False,
    }
    
    ELEMENTS.append(shape)
    ELEMENTS.append(text_element)
    return shape_id

def create_arrow(start_id, end_id, label=None):
    arrow_id = get_id()
    
    # Simple straight arrow logic for vertical flow
    # In a real app, we'd calculate points based on positions
    # Here we assume standard vertical spacing
    
    arrow = {
        "id": arrow_id,
        "type": "arrow",
        "x": 0, "y": 0, # Placeholder, Excalidraw re-calcs based on bindings usually
        "width": 100, "height": 100,
        "angle": 0,
        "strokeColor": THEME["accent"],
        "backgroundColor": "transparent",
        "fillStyle": "hachure",
        "strokeWidth": 2,
        "strokeStyle": "solid",
        "roughness": 0,
        "opacity": 100,
        "groupIds": [],
        "roundness": {"type": 2},
        "seed": 1,
        "version": 1,
        "versionNonce": 0,
        "isDeleted": False,
        "boundElements": None,
        "updated": int(time.time() * 1000),
        "link": None,
        "locked": False,
        "points": [[0, 0], [0, 60]], # Vertical drop
        "startBinding": {"elementId": start_id, "focus": 0, "gap": 10},
        "endBinding": {"elementId": end_id, "focus": 0, "gap": 10},
        "startArrowhead": None,
        "endArrowhead": "arrow"
    }
    
    ELEMENTS.append(arrow)
    
    # Update bound elements of connected shapes
    for el in ELEMENTS:
        if el["id"] in [start_id, end_id]:
            if el["boundElements"] is None:
                el["boundElements"] = []
            el["boundElements"].append({"type": "arrow", "id": arrow_id})

def generate():
    start_x = 400
    start_y = 50
    gap_y = 140 # 80 height + 60 gap
    
    # 1. Start
    id_start = create_shape("ellipse", start_x, start_y, 120, 60, "Start: Application")
    
    # 2. Create Profile
    id_profile = create_shape("rectangle", start_x - 20, start_y + gap_y, 160, 80, "Create Member Profile")
    create_arrow(id_start, id_profile)
    
    # 3. Upload Docs
    id_upload = create_shape("rectangle", start_x - 20, start_y + gap_y * 2, 160, 80, "Upload Documents")
    create_arrow(id_profile, id_upload)
    
    # 4. Decision: Docs Valid
    id_dec_docs = create_shape("diamond", start_x - 10, start_y + gap_y * 3, 140, 100, "Docs Valid?", True)
    create_arrow(id_upload, id_dec_docs)
    
    # 5. Novitiate
    id_novitiate = create_shape("rectangle", start_x - 20, start_y + gap_y * 4 + 20, 160, 80, "Novitiate Period")
    create_arrow(id_dec_docs, id_novitiate, "Yes")
    
    # 6. First Vows
    id_vows1 = create_shape("rectangle", start_x - 20, start_y + gap_y * 5 + 20, 160, 80, "First Vows")
    create_arrow(id_novitiate, id_vows1)
    
    # 7. Decision: Final Vows
    id_dec_final = create_shape("diamond", start_x - 10, start_y + gap_y * 6 + 20, 140, 100, "Final Vows?", True)
    create_arrow(id_vows1, id_dec_final)
    
    # 8. Assign Community
    id_assign = create_shape("rectangle", start_x - 20, start_y + gap_y * 7 + 40, 160, 80, "Assign Community")
    create_arrow(id_dec_final, id_assign, "Yes")
    
    # 9. Financial Cycle
    id_finance = create_shape("rectangle", start_x - 20, start_y + gap_y * 8 + 40, 160, 80, "Monthly Financials")
    create_arrow(id_assign, id_finance)
    
    # 10. Decision: Transfer
    id_dec_transfer = create_shape("diamond", start_x - 10, start_y + gap_y * 9 + 40, 140, 100, "Transfer?", True)
    create_arrow(id_finance, id_dec_transfer)
    
    # 11. Strategic Oversight
    id_oversight = create_shape("rectangle", start_x - 20, start_y + gap_y * 10 + 60, 160, 80, "Strategic Oversight")
    create_arrow(id_dec_transfer, id_oversight, "No")
    
    # 12. End
    id_end = create_shape("ellipse", start_x, start_y + gap_y * 11 + 60, 120, 60, "End: Retirement")
    create_arrow(id_oversight, id_end)
    
    # Output
    output = {
        "type": "excalidraw",
        "version": 2,
        "source": "bmad-agent",
        "elements": ELEMENTS,
        "appState": {
            "viewBackgroundColor": "#ffffff",
            "gridSize": 20
        },
        "files": {}
    }
    
    print(json.dumps(output, indent=2))

if __name__ == "__main__":
    generate()

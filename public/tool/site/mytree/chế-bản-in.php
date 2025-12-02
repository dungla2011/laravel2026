<?php


require_once '/var/www/html/public/index.php';

if(!$uid = getCurrentUserId()){
    die("NOT LOGIN?");
}

if(!$idf = request('idf')){
    die("NOT VALID ID?");
}
$idf0 = $idf;

if(is_numeric($idf)){
//    die("NOT VALID IDF?");
}

$idf = qqgetIdFromRand_( $idf );

if(!$gp = \App\Models\GiaPha::find($idf)){
    die("NOT FOUND IDF?");
}

if($gp->married_with){
    $idf0 = qqgetRandFromId_($gp->married_with);
    $idf = $gp->married_with;
    if(!$gp = \App\Models\GiaPha::find($idf)){
        die("NOT FOUND IDF2?");
    }
}


$nameTree = strip_tags($gp->name);
$nameTree = $nameTree ?? "Cây-Phả-Hệ";
$nameTree .= ',Time='.date('Y-m-d_H-i-s');

if(!isSupperAdmin_())
if($gp->user_id != $uid){
    die("Không phải dữ liệu của bạn?");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chế bản in Gia Phả</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        svg {
            border: 1px solid #ccc;
            display: block;
            margin: auto;
        }
        body {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
            font-family: Tahoma;
            font-size: 95%;
        }

        * {
            box-sizing: border-box;
            margin: 0px;
            padding: 0px;
        }

        .menu1 button{
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

    </style>
</head>
<body>
<div class="container-fluid mt-3">
    <div class="alert alert-info">
        <div>
            Phần Chế bản in để tự chỉnh lại Cây phả hệ theo ý muốn của bạn, sau đó bạn có thể tải về để in ấn hoặc lưu trữ.
            Cây được chỉnh lại theo chiều dọc Như bên dưới để giảm kích thước ngang, sau đó có thể chỉnh lại theo ý muốn
            <br>
            Tải file cây Gia phả này về máy tính, Sau đó mở Trình Vẽ chế bản, chọn File -> Open from -> Device -> Chọn file vừa tải về để Chỉnh sửa:

        </div>
        <div class="mt-2">
        <button id="exportDrawio" class="btn btn-success mr-2"> Tải file cây Gia phả này về máy tính </button>
        <a href="https://draw.mytree.vn" target="_blank" class="btn btn-primary">  Mở trình Vẽ chế bản </a>
        </div>
      <div class="mt-2"><a target="_blank" href="https://www.youtube.com/watch?v=0drWv3mAiqA&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=17"> Xem thêm Video hướng dẫn Tại đây </a> </div>

    </div>
    <svg id="familyTreeSvg" width="100%" height="800" style="background-color: lavender"></svg>
</div>
<script>


    function sortFamilyTree(data) {
        // Tạo map để tra cứu nhanh theo id
        const idMap = new Map(data.map(item => [item.id, item]));

        // Tạo map để lưu danh sách người phối ngẫu theo id
        const spousesMap = new Map();

        // Xây dựng map các cặp vợ chồng
        data.forEach(person => {
            if (person.married_with) {
                // Thêm người phối ngẫu vào danh sách
                if (!spousesMap.has(person.married_with)) {
                    spousesMap.set(person.married_with, []);
                }
                spousesMap.get(person.married_with).push(person.id);

                // Thêm người hiện tại vào danh sách phối ngẫu của người kia
                if (!spousesMap.has(person.id)) {
                    spousesMap.set(person.id, []);
                }
                spousesMap.get(person.id).push(person.married_with);
            }
        });

        // Tạo mảng kết quả mới
        const result = [];
        const added = new Set();

        // Hàm thêm người và các phối ngẫu vào kết quả
        function addPersonAndSpouses(personId) {
            if (added.has(personId)) return;

            // Thêm người hiện tại
            const person = idMap.get(personId);
            result.push(person);
            added.add(personId);

            // Thêm các phối ngẫu
            const spouses = spousesMap.get(personId) || [];
            spouses.forEach(spouseId => {
                if (!added.has(spouseId)) {
                    const spouse = idMap.get(spouseId);
                    result.push(spouse);
                    added.add(spouseId);
                }
            });
        }

        // Duyệt qua từng người trong dữ liệu gốc
        data.forEach(person => {
            addPersonAndSpouses(person.id);
        });

        return result;
    }

    function centerTree(svgElement, treeData) {
        const svgWidth = svgElement.node().getBoundingClientRect().width;

        let minX = Infinity;
        let maxX = -Infinity;
        let minY = Infinity;
        let maxY = -Infinity;

        treeData.descendants().forEach(d => {
            minX = Math.min(minX, d.x);
            maxX = Math.max(maxX, d.x);
            minY = Math.min(minY, d.y);
            maxY = Math.max(maxY, d.y);
        });

        const treeWidth = maxX - minX;
        const xOffset = (svgWidth - treeWidth) / 2 - minX;
        const yOffset = 100 - minY;

        const svgGroup = svgElement.select("g");

        svgGroup.selectAll(".node")
            .attr("transform", d => `translate(${d.x + xOffset},${d.y + yOffset})`);

        svgGroup.selectAll(".link")
            .attr("x1", d => d.source.x + xOffset)
            .attr("y1", d => d.source.y + yOffset)
            .attr("x2", d => d.target.x + xOffset)
            .attr("y2", d => d.target.y + yOffset);

        svgGroup.selectAll(".spouse-link")
            .attr("x1", d => d.source.x + xOffset)
            .attr("y1", d => d.source.y + yOffset)
            .attr("x2", d => d.target.x + xOffset)
            .attr("y2", d => d.target.y + yOffset);

        return svgGroup;
    }

    function addSpouseLinks(svgGroup, treeData) {
        const idToNode = {};
        treeData.descendants().forEach(node => {
            idToNode[node.data.id] = node;
        });

        const spouseLinks = [];
        treeData.descendants().forEach(node => {
            if (node.data.married_with && idToNode[node.data.married_with]) {
                // Xác định bên trái và bên phải
                const leftNode = node.x < idToNode[node.data.married_with].x ? node : idToNode[node.data.married_with];
                const rightNode = node.x < idToNode[node.data.married_with].x ? idToNode[node.data.married_with] : node;

                spouseLinks.push({
                    source: leftNode,
                    target: rightNode
                });
            }
        });

        svgGroup.selectAll(".spouse-link")
            .data(spouseLinks)
            .enter()
            .append("line")
            .attr("class", "spouse-link")
            // Điểm bắt đầu: mép phải của node bên trái
            .attr("x1", d => d.source.x + 200 + nodeWidth/2)
            .attr("y1", d => d.source.y + 50)
            // Điểm kết thúc: mép trái của node bên phải
            .attr("x2", d => d.target.x + 200 - nodeWidth/2)
            .attr("y2", d => d.target.y + 50)
            .attr("stroke", "red")
            .attr("stroke-dasharray", "2,2");
    }

    // Cập nhật hàm centerTree để xử lý đúng các đường nối vợ chồng
    function centerTree(svgElement, treeData) {
        // Lấy kích thước thực của SVG
        const svgWidth = svgElement.node().getBoundingClientRect().width;

        // Tìm root node
        const rootNode = treeData.descendants()[0];

        // Tính toán offset để đặt root node vào giữa màn hình
        const xOffset = (svgWidth / 2) - rootNode.x - 200; // Trừ 200 vì có transform ban đầu
        const yOffset = 100 - rootNode.y; // Đặt root node cách top 100px

        const svgGroup = svgElement.select("g");

        // Cập nhật vị trí các node
        svgGroup.selectAll(".node")
            .attr("transform", d => `translate(${d.x + xOffset},${d.y + yOffset})`);

        // Cập nhật vị trí các đường nối cha-con
        svgGroup.selectAll(".link")
            .attr("d", d => {
                const sourceX = d.source.x + xOffset;
                const sourceY = d.source.y + yOffset;
                const targetX = d.target.x + xOffset;
                const targetY = d.target.y + yOffset;
                const midY = sourceY + (targetY - sourceY) / 2;

                return `
                M ${sourceX},${sourceY}
                V ${midY}
                H ${targetX}
                V ${targetY}
            `;
            });

        // Cập nhật vị trí các đường nối vợ chồng
        svgGroup.selectAll(".spouse-link")
            .attr("x1", d => d.source.x + xOffset + nodeWidth / 2)
            .attr("y1", d => d.source.y + yOffset)
            .attr("x2", d => d.target.x + xOffset - nodeWidth / 2)
            .attr("y2", d => d.target.y + yOffset);

        return svgGroup;
    }

    const rootId = 0;
    let data = [
        { "id": 321525, "parent_id": rootId, "name": "A0", "married_with": null, "gender": 1 },
        { "id": 321543, "parent_id": rootId, "name": "B0", "married_with": 321525, "gender": 1 },
        { "id": 321526, "parent_id": 321525, "name": "A1", "married_with": null, "gender": 1 },
        { "id": 321531, "parent_id": 321525, "name": "A2", "married_with": null, "gender": 1 },
    ];

    let link = 'https://mytree.vn/train/mytree-lab/train-step-by-step/data.php?id=zc327648'



    link = '/tool/site/mytree/get-data.php?idf=<?php echo $idf0?>'

    console.log("LINK = " + link);

    const nodeWidth = 30;
    const nodeHeight = 200;
    const horizontalSpacing = 10;
    const verticalSpacing = 50;

    let treeData;
    let svgGroup

    fetch(link)
        .then(response => response.json())
        .then(json => {
            data = json;

            console.log("Data API = ", data);


            data = sortFamilyTree(data);
            console.log("DataSort = ", data);

            function buildTree(data, rootId) {
                const idToNode = {};
                data.forEach(node => {
                    idToNode[node.id] = {...node, children: []};
                });

                data.forEach(node => {
                    if (idToNode[node.parent_id]) {
                        idToNode[node.parent_id].children.push(idToNode[node.id]);
                    }
                });

                console.log(" idToNode = ", idToNode);

                return idToNode[rootId] || null;
            }

            const root = buildTree(data, rootId);
            console.log(" Root = ", rootId, root);
            treeData = d3.hierarchy(root, d => d.children);
            const treeLayout = d3.tree().nodeSize([nodeWidth + horizontalSpacing, nodeHeight + verticalSpacing]);
            treeLayout(treeData);

            const svg = d3.select("#familyTreeSvg")
                .call(d3.zoom().on("zoom", (event) => {
                    svgGroup.attr("transform", event.transform);
                }));

            svgGroup = svg.append("g");

            addSpouseLinks(svgGroup, treeData);

            svgGroup.selectAll(".link")
                .data(treeData.links())
                .enter()
                .append("path")
                .attr("class", "link")
                .attr("d", d => {
                    const sourceX = d.source.x + 200;
                    const sourceY = d.source.y + 50;
                    const targetX = d.target.x + 200;
                    const targetY = d.target.y + 50;
                    const midY = sourceY + (targetY - sourceY) / 2; // Điểm giữa theo trục Y

                    // Tạo đường Hubline
                    return `
            M ${sourceX},${sourceY}
            V ${midY}
            H ${targetX}
            V ${targetY}
        `;
                })
                .attr("stroke", "#ccc")
                .attr("fill", "none");

            const nodes = svgGroup.selectAll(".node")
                .data(treeData.descendants())
                .enter()
                .append("g")
                .attr("class", "node")
                .attr("transform", d => `translate(${d.x + 200},${d.y + 50})`);

            nodes.append("rect")
                .attr("width", nodeWidth)
                .attr("height", nodeHeight)
                .attr("x", -nodeWidth / 2)
                .attr("y", -nodeHeight / 2)
                .attr("fill", "#fff")
                .attr("stroke", "#000");

// Thay đổi phần nodes.append("text") thành: xxx
            nodes.append("text")
                .attr("text-anchor", "start")  // Đặt text-anchor là start thay vì middle
                .attr("transform", d => `translate(${-nodeWidth/2 + 12},${-nodeHeight/2 + 15}) rotate(90)`)  // Điều chỉnh vị trí bắt đầu
                .text(d => d.data.name);

            centerTree(svg, treeData);

        });
</script>

<script>


    function toDrawioXML(nodes, links) {
        const xmlParts = [];
        xmlParts.push('<?xml version="1.0" encoding="UTF-8"?>');
        xmlParts.push('<mxfile host="app.diagrams.net" modified="2024-01-01T00:00:00.000Z" agent="Mozilla/5.0" version="21.1.0" type="device">');
        xmlParts.push('<diagram name="Family Tree" id="family-tree">');
        xmlParts.push('<mxGraphModel dx="1000" dy="600" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="850" pageHeight="1100">');
        xmlParts.push('<root>');
        xmlParts.push('<mxCell id="0"/>');
        xmlParts.push('<mxCell id="1" parent="0"/>');

        // Add nodes
        nodes.forEach(node => {
            const nodeId = `node-${node.data.id}`;
            const x = Math.round(node.x + 200 - nodeWidth/2);
            const y = Math.round(node.y + 50 - nodeHeight/2);
            const name = node.data.name;

            // Thêm styleClass cho node - "family-node" là class chung, "gender-1" hoặc "gender-2" là class theo giới tính
            const genderClass = `gender-${node.data.gender}`;
            xmlParts.push(`<mxCell id="${nodeId}" value="${name}" vertex="1" parent="1"
            style="styleClass=family-node ${genderClass}">
            <mxGeometry x="${x}" y="${y}" width="${nodeWidth}" height="${nodeHeight}" as="geometry"/>
        </mxCell>`);
        });

        // Add parent-child connections
        links.forEach((link, index) => {
            const sourceId = `node-${link.source.data.id}`;
            const targetId = `node-${link.target.data.id}`;

            xmlParts.push(`<mxCell id="edge-${index}" edge="1" parent="1" source="${sourceId}" target="${targetId}"
            style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;endArrow=none;endFill=0;exitX=0.5;exitY=1;entryX=0.5;entryY=0;exitDx=0;exitDy=0;entryDx=0;entryDy=0;styleClass=parent-child-edge">
            <mxGeometry relative="1" as="geometry">
                <mxPoint as="sourcePoint"/>
                <mxPoint as="targetPoint"/>
            </mxGeometry>
        </mxCell>`);
        });

        // Add spouse connections
        nodes.forEach(node => {
            if (node.data.married_with) {
                const spouse = nodes.find(n => n.data.id === node.data.married_with);
                if (spouse && node.x < spouse.x) {
                    xmlParts.push(`<mxCell id="spouse-${node.data.id}-${spouse.data.id}" edge="1" parent="1"
                    source="node-${node.data.id}" target="node-${spouse.data.id}"
                    style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;endArrow=none;endFill=0;dashed=1;dashPattern=1 4;strokeColor=#FF0000;exitX=1;exitY=0.5;entryX=0;entryY=0.5;exitDx=0;exitDy=0;entryDx=0;entryDy=0;styleClass=spouse-edge">
                    <mxGeometry relative="1" as="geometry">
                        <mxPoint as="sourcePoint"/>
                        <mxPoint as="targetPoint"/>
                    </mxGeometry>
                </mxCell>`);
                }
            }
        });

        xmlParts.push('</root>');
        xmlParts.push('</mxGraphModel>');
        xmlParts.push('</diagram>');
        xmlParts.push('</mxfile>');

        return xmlParts.join('\n');
    }

    // Hàm tải file XML
    function downloadDrawioXML(xml) {
        const blob = new Blob([xml], { type: 'text/xml;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '<?php echo $nameTree ?>.xml';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function exportTest(){
        const nodes = treeData.descendants();
        const links = treeData.links();
        const drawioXML = toDrawioXML(nodes, links);

        //Post drawioXML to server to save to file
        console.log(drawioXML);

        const formData = new FormData();
        formData.append('xml', drawioXML);

        <?php if(0) { ?>
        fetch('https://mytree.vn/tool/testing/post-xml-draw-io.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                document.getElementById('export_status').innerHTML = 'Exported to draw.io: ' + data;
                console.log('Success:', data);

                document.getElementById('export_status').innerHTML += ' , (Không tải về PC), URL = ' +
                    '<a target="_blank" href="https://draw.mytree.vn/?url=https://mytree.vn/tool/testing/drawio1.xml"> LINK </a>';
            })
            .catch((error) => {
                console.error('Error:', error);
            });

        <?php } ?>
        downloadDrawioXML(drawioXML);
    }

    // Gắn sự kiện vào nút Export
    document.getElementById('exportDrawio').addEventListener('click', () => {
        exportTest()

    });

</script>

</body>
</html>

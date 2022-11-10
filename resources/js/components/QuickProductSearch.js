import React, { useEffect, useMemo, useRef } from "react";
import ReactDOM from "react-dom";
import { MDBDataTable, MDBDataTableV5 } from "mdbreact";
import toast, { Toaster } from "react-hot-toast";
import Axios from "axios";
import { BsFillCheckCircleFill } from "react-icons/bs";

export default function QuickSearchTable() {
    const [datatable, setDatatable] = React.useState({
        columns: [
            {
                label: ` `,
                field: "add_cart",
                width: 150,
                sort: "disabled",
                attributes: {
                    "aria-controls": "DataTable",
                    "aria-label": "ADD ALL",
                },
            },
            {
                label: "QTY",
                field: "quantity",
                sort: "disabled",
                width: 100,
            },
            {
                label: "IMAGE",
                field: "image",
                sort: "disabled",
                width: 200,
            },
            {
                label: "PRODUCT",
                field: "name",
                sort: "disabled",
                width: 100,
            },
            {
                label: "SKU #",
                field: "number",
                sort: "disabled",
                width: 200,
            },
            {
                label: "NAME",
                field: "color",
                sort: "disabled",
                width: 100,
            },
            {
                label: "PRICE",
                field: "price",
                sort: "disabled",
                width: 150,
            },
        ],
        rows: [],
    });

    const [keyword, setKeyword] = React.useState("");

    const addCart = (item) => {
        const quantity = document.getElementById(item.id_pdf).value;

        // api block for creating new cart item

        const url = "/item-add";

        let cutFee = "None";

        if (item.cut_fee.indexOf("Fee A") !== false) {
            cutFee = 3;
        }
        if (item.cut_fee.indexOf("Fee B") !== false) {
            cutFee = 30;
        }

        const params = {
            itemnum: item.item_number,
            itemname: item.item_name,
            itempdfid: item.id_pdf,
            itemunit: item.selling_unit,
            itemcolor: item.color_name,
            price: item.wholesale_price,
            qty: quantity,
            cutFee: cutFee,
            type: "sample",
        };

        Axios({
            method: "post",
            url: url,
            data: params,
            config: {
                headers: {
                    "Content-Type": "application/json",
                },
            },
        })
            .then((res) => {
                toast.success("Order Sample Success.");

                let cartSampleCount = document.getElementById("cart-count");
                let mobileCartSampleCount =
                    document.getElementById("mobile-cart-count");

                cartSampleCount.classList.add("red-dot");
                mobileCartSampleCount.classList.add("red-dot");
                return res;
            })
            .catch((err) => {
                toast.error("Order Sample Failed.");
                console.log(err);
            });
    };

    const validate = ({ id_pdf }) => {
        const quantity = document.getElementById(id_pdf).value;

        if (quantity < 0) {
            toast.error("Please input valid quantity!");
            return false;
        }
    };

    const getUrl = (itemName) => {
        let url = "";

        if (itemName != "alchemy+") {
            url = itemName.replace(" ", "-").toLowerCase();
        }

        return url.toLowerCase();
    };

    const addFieldToObjectArray = (arrayData) => {
        let resultArray = [];
        for (const item of arrayData) {
            resultArray.push({
                // ...item, add_cart: <button className="btn btn-danger" onClick={() => { addCart({ ...item }) }} disabled={item.inventoried > 0 ? false : true}>ADD CART</button>, quantity: <input type="number" id={item.id_pdf} onChange={() => { validate({ ...item }) }} defaultValue={item.min_selling_quantity}></input>, in_stock: item.inventoried > 0 ? <>IN STOCK <BsFillCheckCircleFill /></> : ``, image: <BlurredImage imageUrl={"https://www.innovationsusa.com/storage/sku/150x150/" + item.item_number + ".jpg?v=0.6"}></BlurredImage>
                ...item,
                add_cart: (
                    <button
                        className="btn btn-danger"
                        onClick={() => {
                            addCart({ ...item });
                        }}
                        disabled={item.inventoried > 0 ? false : true}
                    >
                        Order Sample
                    </button>
                ),
                quantity: (
                    <input
                        type="number"
                        id={item.id_pdf}
                        onChange={() => {
                            validate({ ...item });
                        }}
                        defaultValue={1}
                    ></input>
                ),
                price: `$${item.wholesale_price}/yd`,
                image: (
                    <BlurredImage
                        imageUrl={
                            "https://www.innovationsusa.com/storage/sku/150x150/" +
                            item.item_number +
                            ".jpg?v=0.6"
                        }
                    ></BlurredImage>
                ),
                name: (
                    <a href={`/item/${getUrl(item.item_name)}`}>
                        {item.item_name}
                    </a>
                ),
                number: (
                    <a
                        href={`/item/${getUrl(item.item_name)}/${getUrl(
                            item.item_number
                        )}`}
                    >
                        {item.item_number}
                    </a>
                ),
                color: (
                    <a
                        href={`/item/${getUrl(item.item_name)}/${getUrl(
                            item.item_number
                        )}`}
                    >
                        {item.color_name}
                    </a>
                ),
            });
        }
        return resultArray;
    };

    useEffect(() => {
        const data = window.localStorage.getItem("keyword");
        if (data) setKeyword(data);
    }, []);

    useEffect(() => {
        const fetchProductData = async () => {
            const data = await Axios({
                method: "post",
                url: "/get-product-data",
                config: {
                    headers: {
                        "Content-Type": "application/json",
                    },
                },
            })
                .then((res) => {
                    return res.data;
                })
                .catch((err) => {
                    return [];
                });

            setDatatable({ ...datatable, rows: addFieldToObjectArray(data) });
        };

        const fetchSearchData = async () => {
            const params = {
                keyword: keyword,
            };
            const data = await Axios({
                method: "post",
                url: "/get-searched-products",
                data: params,
                config: {
                    headers: {
                        "Content-Type": "application/json",
                    },
                },
            })
                .then((res) => {
                    return res.data.data;
                })
                .catch((err) => {
                    return [];
                });
            setDatatable({ ...datatable, rows: addFieldToObjectArray(data) });
        };

        if (!keyword.trim().length) {
            fetchProductData();
        } else {
            fetchSearchData();
        }

        window.localStorage.setItem("keyword", keyword);

        return () => {};
    }, [keyword]);

    return (
        <>
            <div className="col-md-6 col-sm-12 float-left">
                <input
                    type="text"
                    className="col-md-4 product-search-bar"
                    value={keyword}
                    onChange={(e) => setKeyword(e.target.value)}
                    placeholder={`Search...`}
                    style={{ color: "black" }}
                ></input>
            </div>
            <MDBDataTable
                entriesLabel={"SKUs Per Page"}
                entriesOptions={[10, 25, 50, 100]}
                entries={10}
                pagesAmount={4}
                data={datatable}
                searching={false}
                paging={true}
            />
            <Toaster />
        </>
    );
}

const useProgressiveImg = (lowQualitySrc, highQualitySrc) => {
    const [src, setSrc] = React.useState(lowQualitySrc);
    React.useEffect(() => {
        setSrc(lowQualitySrc);
        const img = new Image();
        img.src = highQualitySrc;
        img.onload = () => {
            setSrc(highQualitySrc);
        };
    }, [lowQualitySrc, highQualitySrc]);
    return [src, { blur: src === lowQualitySrc }];
};

const BlurredImage = ({ imageUrl }) => {
    const [src, { blur }] = useProgressiveImg("/images/preload.png", imageUrl);
    return (
        <img
            src={src}
            style={{
                width: 200,
                filter: blur ? "blur(20px)" : "none",
                transition: blur ? "none" : "filter 0.3s ease-out",
                borderRadius: "10px",
            }}
        />
    );
};

if (document.getElementById("quickproductContainer")) {
    ReactDOM.render(
        <QuickSearchTable />,
        document.getElementById("quickSearchTable")
    );
}

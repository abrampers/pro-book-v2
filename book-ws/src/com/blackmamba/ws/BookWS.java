package com.blackmamba.ws;

import javax.jws.WebMethod;
import javax.jws.WebService;
import javax.jws.soap.SOAPBinding;
import javax.jws.soap.SOAPBinding.Style;
import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.HashMap;
import java.util.Map;

import com.blackmamba.model.BookDetail;
import org.json.simple.JSONObject;

import com.blackmamba.model.Book;

import com.blackmamba.api.GoogleBook;

@WebService
@SOAPBinding(style= Style.RPC, use= SOAPBinding.Use.ENCODED)
public class BookWS {
    @WebMethod
    public String searchTitle(String title) {
        return "Hello, World JAX-WS " + title + " HUYUUUU";
    }

    @WebMethod
    public BookDetail searchDetail(String id) {
        System.out.println(id);
        GoogleBook googleBook = new GoogleBook();
        BookDetail bookDetail = googleBook.getBookDetail(id);
//        Book book = new Book(id, 400000);
        return bookDetail;
    }

//    @WebMethod

}


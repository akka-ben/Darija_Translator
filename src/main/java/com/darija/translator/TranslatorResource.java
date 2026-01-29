package com.darija.translator;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;

import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;

@Path("/translator")
public class TranslatorResource {

    private static final String OPENROUTER_API_KEY = System.getenv("OPENROUTER_API_KEY");
    private static final String OPENROUTER_URL = "https://openrouter.ai/api/v1/chat/completions";

    @GET
    @Path("/health")
    @Produces(MediaType.TEXT_PLAIN)
    public String health() {
        return "Darija Translator API is running (LLM enabled)";
    }

    @POST
    @Path("/translate")
    @Consumes(MediaType.TEXT_PLAIN)
    @Produces(MediaType.TEXT_PLAIN)
    public Response translate(String text) {

        if (text == null || text.isBlank()) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("Text must not be empty")
                    .build();
        }

        try {
            String result = callLLM(text);
            return Response.ok(result).build();
        } catch (Exception e) {
            e.printStackTrace();
            return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                    .entity("LLM error: " + e.getMessage())
                    .build();
        }
    }

    private String callLLM(String text) throws Exception {

        if (OPENROUTER_API_KEY == null) {
            throw new Exception("OPENROUTER_API_KEY not set");
        }

        String prompt =
            "You are a native Moroccan who speaks Darija DAILY.\n"
            + "You were born and raised in Morocco.\n\n"

            + "TASK:\n"
            + "Translate English into REAL Moroccan Darija as SPOKEN in Morocco.\n\n"

            + "IMPORTANT OUTPUT REQUIREMENT (MANDATORY):\n"
            + "You MUST output the translation in TWO forms:\n"
            + "1) Moroccan Darija written in ARABIC letters (dialect, not Modern Standard Arabic)\n"
            + "2) The SAME Darija written in LATIN letters (Darija transliteration)\n\n"

            + "STRICT RULES (VERY IMPORTANT):\n"
            + "- DO NOT use Modern Standard Arabic (Fusha).\n"
            + "- DO NOT use formal Arabic words like 'kayfa', 'hal', 'tahaddatha'.\n"
            + "- Use ONLY spoken Moroccan Darija used in daily life.\n"
            + "- Short, informal, natural sentences.\n"
            + "- No French.\n"
            + "- No explanations.\n"
            + "- No emojis.\n\n"

            + "OUTPUT FORMAT (MANDATORY):\n"
            + "First line: Moroccan Darija written in ARABIC letters\n"
            + "Second line: The SAME Darija written in LATIN letters\n"
            + "- No labels\n"
            + "- No prefixes\n"
            + "- No extra text\n"
            + "- Exactly two lines\n\n"

            + "====================\n"
            + "EXAMPLES (VERY IMPORTANT – FOLLOW THEM EXACTLY)\n"
            + "====================\n\n"

            + "English: Hello\n"
            + "سلام\n"
            + "salam\n\n"

            + "English: How are you?\n"
            + "كيف داير؟\n"
            + "kidayr?\n\n"

            + "English: How are you doing today?\n"
            + "لاباس عليك اليوم؟\n"
            + "labas 3lik lyom?\n\n"

            + "English: Thank you\n"
            + "شكرا\n"
            + "chokran\n\n"

            + "English: Thank you very much\n"
            + "شكرا بزاف\n"
            + "chokran bzaf\n\n"

            + "English: You're welcome\n"
            + "مرحبا\n"
            + "marhba\n\n"

            + "English: What are you doing?\n"
            + "شنو كتدير؟\n"
            + "shnu katdir?\n\n"

            + "English: Where are you going?\n"
            + "فين غادي؟\n"
            + "fin ghadi?\n\n"

            + "English: I am going home\n"
            + "غادي للدار\n"
            + "ghadi lddar\n\n"

            + "English: I don't know\n"
            + "ما عرفتشي\n"
            + "ma 3reftch\n\n"

            + "English: It's okay / No problem\n"
            + "ماشي مشكل\n"
            + "mashi mochkil\n\n"

            + "English: I'm tired\n"
            + "عييت\n"
            + "3yيت\n\n"

            + "English: See you later\n"
            + "نشوفك من بعد\n"
            + "nchoufek mn b3d\n\n"

            + "English: Are you coming?\n"
            + "واش جاي؟\n"
            + "wash jay?\n\n"

            + "English: Let's go\n"
            + "يلا نمشيو\n"
            + "yalla nmchiw\n\n"

            + "====================\n"
            + "NOW TRANSLATE THIS SENTENCE:\n"
            + text;

        String json =
            "{"
          + "\"model\":\"meta-llama/llama-3.1-8b-instruct\","
          + "\"messages\":["
          + " {\"role\":\"user\",\"content\":\"" + prompt.replace("\"", "\\\"") + "\"}"
          + "]"
          + "}";

        URL url = new URL(OPENROUTER_URL);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();

        conn.setRequestMethod("POST");
        conn.setRequestProperty("Authorization", "Bearer " + OPENROUTER_API_KEY);
        conn.setRequestProperty("Content-Type", "application/json");
        conn.setRequestProperty("HTTP-Referer", "http://localhost");
        conn.setRequestProperty("X-Title", "Darija Translator");
        conn.setDoOutput(true);

        try (OutputStream os = conn.getOutputStream()) {
            os.write(json.getBytes(StandardCharsets.UTF_8));
        }

        if (conn.getResponseCode() != 200) {
            throw new Exception("HTTP " + conn.getResponseCode());
        }

        String response = new BufferedReader(
                new InputStreamReader(conn.getInputStream()))
                .lines()
                .reduce("", (a, b) -> a + b);

        return extractText(response);
    }

    private String extractText(String json) {
        int i = json.indexOf("\"content\":\"");
        if (i == -1) return "No translation";
        i += 11;
        int j = json.indexOf("\"", i);
        return json.substring(i, j)
                .replace("\\n", "\n")
                .replace("\\\"", "\"");
    }
}